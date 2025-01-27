const occurrenceDataUploadModule = {
    props: {
        collid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="processor-container">
            <div class="processor-control-container">
                <q-card class="processor-control-card">
                    <q-list class="processor-control-accordion">
                        <q-expansion-item class="overflow-hidden" group="expansiongroup" label="Configuration" header-class="bg-grey-3 text-bold" default-opened>
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    <div class="column q-col-gutter-sm">
                                        <div class="row justify-between q-col-gutter-sm">
                                            <div class="col-12 col-sm-9">
                                                <template v-if="collectionDataUploadParametersArr.length > 0">
                                                    <selector-input-element :disabled="currentTab !== 'configuration' || currentProcess" label="Select Upload Profile" :options="collectionDataUploadParametersArr" option-value="uspid" option-label="title" :value="collectionDataUploadParametersId" @update:value="(value) => processParameterProfileSelection(value)"></selector-input-element>
                                                </template>
                                            </div>
                                            <div class="col-12 col-sm-3 row justify-end">
                                                <div>
                                                    <q-btn color="secondary" @click="showCollectionDataUploadParametersEditorPopup = true" :label="Number(collectionDataUploadParametersId) > 0 ? 'Edit' : 'Create'" :disabled="currentTab !== 'configuration' || currentProcess" dense />
                                                </div>
                                            </div>
                                        </div>
                                        <collection-data-upload-parameters-field-module :disabled="currentTab !== 'configuration' || currentProcess"></collection-data-upload-parameters-field-module>
                                        <div v-if="Number(profileData.uploadtype) === 6" class="row">
                                            <div class="col-grow">
                                                <file-picker-input-element :disabled="currentTab !== 'configuration' || currentProcess" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="false" @update:file="(value) => uploadedFile = value[0]"></file-picker-input-element>
                                            </div>
                                        </div>
                                        <div class="row justify-end">
                                            <div>
                                                <q-btn color="secondary" @click="initializeUpload();" label="Initialize Upload" :disabled="currentTab !== 'configuration' || currentProcess" dense />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                        <q-separator></q-separator>
                        <q-expansion-item :model-value="currentTab === 'mapping'" class="overflow-hidden" group="expansiongroup" label="Field Mapping" header-class="bg-grey-3 text-bold" :disable="currentTab !== 'mapping' && currentTab !== 'summary'">
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    <div class="column">
                                        <template v-if="flatFileMode">
                                            <div class="q-mb-sm q-pl-sm">
                                                <span class="text-body1 text-bold">Occurrence records</span> <span class="cursor-pointer" @click="openFieldMapperPopup('flat-file');">(view mapping)</span>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="q-mb-sm row q-gutter-sm">
                                                <div class="text-body1 text-bold">Occurrence Records</div>
                                                <div class="cursor-pointer" @click="openFieldMapperPopup('occurrence');">(view mapping)</div>
                                            </div>
                                            <template v-if="determinationDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeDeterminationData" @update:value="(value) => includeDeterminationData = value" :disabled="currentTab !== 'mapping' || currentProcess"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Identification History</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('determination');">(view mapping)</div>
                                                </div>
                                            </template>
                                            <template v-if="multimediaDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeMultimediaData" @update:value="(value) => includeMultimediaData = value" :disabled="currentTab !== 'mapping' || currentProcess"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Media Records</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('multimedia');">(view mapping)</div>
                                                </div>
                                            </template>
                                            <template v-if="mofDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeMofData" @update:value="(value) => includeMofData = value" :disabled="currentTab !== 'mapping' || currentProcess"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Measurement or Fact Records</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('mof');">(view mapping)</div>
                                                </div>
                                            </template>
                                        </template>
                                        <div class="q-mt-sm">
                                            <selector-input-element label="Incoming Records Processing Status" :options="processingStatusOptions" :value="selectedProcessingStatus" @update:value="(value) => selectedProcessingStatus = value" :clearable="true" :disabled="currentTab !== 'mapping' || currentProcess"></selector-input-element>
                                        </div>
                                        <div class="q-mt-sm row justify-end q-gutter-sm">
                                            <div v-if="collectionDataUploadParametersId">
                                                <q-btn color="secondary" @click="saveMapping();" label="Save Mapping" :disabled="currentTab !== 'mapping' || currentProcess" dense />
                                            </div>
                                            <div>
                                                <q-btn color="secondary" @click="startUpload();" label="Start Upload" :disabled="currentTab !== 'mapping' || currentProcess" dense />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                        <q-separator></q-separator>
                        <q-expansion-item :model-value="currentTab === 'summary'" class="overflow-hidden" group="expansiongroup" label="Summary" header-class="bg-grey-3 text-bold" :disable="currentTab !== 'summary' || currentProcess">
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    <div class="column">
                                        <div class="text-body1 text-bold">
                                            Upload Summary
                                        </div>
                                        <div class="row q-col-gutter-xs">
                                            <div>
                                                Occurrence records pending transfer: {{ uploadSummaryData['occur'] }}
                                            </div>
                                            <div v-if="Number(uploadSummaryData['occur']) > 0" class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('occur');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div v-if="Number(uploadSummaryData['occur']) > 0">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('occur', 'upload_occurrence_records');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div class="row q-col-gutter-xs">
                                            <div>
                                                Records to be updated: {{ uploadSummaryData['update'] }}
                                            </div>
                                            <div v-if="Number(uploadSummaryData['update']) > 0" class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('update');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div v-if="Number(uploadSummaryData['update']) > 0">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('update', 'records_to_update');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div class="row q-col-gutter-xs">
                                            <div>
                                                New records: {{ uploadSummaryData['new'] }}
                                            </div>
                                            <div v-if="Number(uploadSummaryData['new']) > 0" class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('new');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div v-if="Number(uploadSummaryData['new']) > 0">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('new', 'new_records');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div v-if="Number(uploadSummaryData['exist']) > 0" class="row q-col-gutter-xs">
                                            <div>
                                                Previously loaded records not included in upload: {{ uploadSummaryData['exist'] }}
                                            </div>
                                            <div class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('exist');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('exist', 'previous_records_not_matching');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div v-if="Number(uploadSummaryData['nulldbpk']) > 0" class="row q-col-gutter-xs">
                                            <div>
                                                Records that have a missing primary identifier: {{ uploadSummaryData['nulldbpk'] }}
                                            </div>
                                            <div class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('nulldbpk');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('nulldbpk', 'missing_primary_identifier_records');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div v-if="Number(uploadSummaryData['dupdbpk']) > 0" class="row q-col-gutter-xs">
                                            <div>
                                                Records that have a duplicate primary identifier: {{ uploadSummaryData['dupdbpk'] }}
                                            </div>
                                            <div class="q-ml-xs">
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processOpenRecordViewerPopup('dupdbpk');" icon="fas fa-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        View records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                            <div>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="processDownloadRecords('dupdbpk', 'duplicate_primary_identifier_records');" icon="fas fa-download" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Download records
                                                    </q-tooltip>
                                                </q-btn>
                                            </div>
                                        </div>
                                        <div v-if="includeDeterminationData" class="row">
                                            <div>
                                                Determination records pending transfer: {{ uploadSummaryData['ident'] }}
                                            </div>
                                        </div>
                                        <div v-if="includeMultimediaData" class="row">
                                            <div>
                                                Media records pending transfer: {{ uploadSummaryData['media'] }}
                                            </div>
                                        </div>
                                        <div v-if="includeMofData" class="row">
                                            <div>
                                                Measurement or fact records pending transfer: {{ uploadSummaryData['mof'] }}
                                            </div>
                                        </div>
                                        <div class="q-mt-sm row justify-end">
                                            <div>
                                                <q-btn color="secondary" @click="finalTransfer();" label="Transfer Records to Central Occurrence Table" :disabled="currentTab !== 'summary' || currentProcess" dense />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                    </q-list>
                </q-card>
            </div>

            <div class="processor-display-container">
                <q-card class="bg-grey-3 q-pa-sm">
                    <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                        <q-list dense>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                <q-item>
                                    <q-item-section>
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollUp();">Show previous 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                            <q-item v-for="proc in processorDisplayArr">
                                <q-item-section>
                                    <div>{{ proc.procText }} <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                    <template v-if="!proc.loading && proc.resultText">
                                        <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                            {{proc.resultText}}
                                        </div>
                                        <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                            {{proc.resultText}}
                                        </div>
                                    </template>
                                    <template v-if="proc.type === 'multi' && proc.subs.length">
                                        <div class="q-ml-sm">
                                            <div v-for="subproc in proc.subs">
                                                <template v-if="subproc.type === 'text' || subproc.type === 'undo'">
                                                    <div>{{ subproc.procText }} <q-spinner v-if="subproc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                                    <template v-if="!subproc.loading && subproc.resultText">
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="text-weight-bold text-green-9">
                                                            <span class="text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                        </div>
                                                        <div v-if="subproc.result === 'error'" class="text-weight-bold text-negative">
                                                            {{subproc.resultText}}
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </q-item-section>
                            </q-item>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                <q-item>
                                    <q-item-section>
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollDown();">Show next 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-list>
                    </q-scroll-area>
                </q-card>
            </div>
        </div>
        <template v-if="showCollectionDataUploadParametersEditorPopup">
            <collection-data-upload-parameters-editor-popup
                :show-popup="showCollectionDataUploadParametersEditorPopup"
                @close:popup="showCollectionDataUploadParametersEditorPopup = false"
            ></collection-data-upload-parameters-editor-popup>
        </template>
        <template v-if="showFieldMapperPopup">
            <field-mapper-popup
                :disabled="currentTab !== 'mapping' || currentProcess"
                :field-mapping="fieldMapperFieldMapping"
                :source-fields="fieldMapperSourceFields"
                :target-fields="fieldMapperTargetFields"
                :show-popup="showFieldMapperPopup"
                @update:field-mapping="processFieldMapperUpdate"
                @close:popup="showFieldMapperPopup = false"
            ></field-mapper-popup>
        </template>
        <template v-if="showUploadDataTableViewerPopup">
            <upload-data-table-viewer-popup
                :columns="popupColumns"
                :data="popupData"
                :load-count="popupLoadCount"
                :page-number="popupPageNumber"
                :total-records="popupTotalRecords"
                :show-popup="showUploadDataTableViewerPopup"
                @update:page-number="getPopupViewerRecords"
                @close:popup="showUploadDataTableViewerPopup = false"
            ></upload-data-table-viewer-popup>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'collection-data-upload-parameters-editor-popup': collectionDataUploadParametersEditorPopup,
        'collection-data-upload-parameters-field-module': collectionDataUploadParametersFieldModule,
        'field-mapper-popup': fieldMapperPopup,
        'file-picker-input-element': filePickerInputElement,
        'selector-input-element': selectorInputElement,
        'upload-data-table-viewer-popup': uploadDataTableViewerPopup
    },
    setup(props) {
        const { hideWorking, parseCsvFile, processCsvDownload, showNotification, showWorking } = useCore();

        const baseStore = useBaseStore();
        const collectionDataUploadParametersStore = useCollectionDataUploadParametersStore();
        const collectionStore = useCollectionStore();
        
        const acceptedFileTypes = ['csv','geojson','txt','zip'];
        const collectionDataUploadParametersArr = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersArr);
        const collectionDataUploadParametersId = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersID);
        const currentProcess = Vue.ref(null);
        const currentTab = Vue.ref('configuration');
        const determinationDataIncluded = Vue.ref(false);
        const eventMofDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const fieldMapperPopupType = Vue.ref(null);
        const fieldMapperFieldMapping = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence' || fieldMapperPopupType.value === 'flat-file'){
                return fieldMappingDataOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return fieldMappingDataDetermiation.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return fieldMappingDataMedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return fieldMappingDataMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMapperSourceFields = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence'){
                return sourceDataFieldsOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'flat-file'){
                return sourceDataFieldsFlatFile.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return sourceDataFieldsDetermination.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return sourceDataFieldsMultimedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return sourceDataFieldsMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMapperTargetFields = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence'){
                return symbiotaFieldOptionsOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'flat-file'){
                return symbiotaFieldOptionsFlatFile.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return symbiotaFieldOptionsDetermination.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return symbiotaFieldOptionsMedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return symbiotaFieldOptionsMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMappingDataDetermiation = Vue.ref({});
        const fieldMappingDataMedia = Vue.ref({});
        const fieldMappingDataMof = Vue.ref({});
        const fieldMappingDataOccurrence = Vue.ref({});
        const flatFileMode = Vue.ref(false);
        const flatFileMofData = Vue.ref([]);
        const flatFileOccurrenceData = Vue.ref([]);
        const includeDeterminationData = Vue.ref(true);
        const includeMultimediaData = Vue.ref(true);
        const includeMofData = Vue.ref(true);
        const localDwcaFileArr = Vue.ref([]);
        const localDwcaServerPath = Vue.ref(null);
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const multimediaDataIncluded = Vue.ref(false);
        const mofDataIncluded = Vue.ref(false);
        const mofEventDataIncluded = Vue.ref(false);
        const mofOccurrenceDataIncluded = Vue.ref(false);
        const occurrenceMofDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
        const popupColumns = Vue.computed(() => {
            const returnArr = [];
            if(popupData.value.length > 0){
                const fields = Object.keys(popupData.value[0]);
                fields.forEach((field) => {
                    returnArr.push({ name: field, label: field, field: field });
                });
            }
            return returnArr;
        });
        const popupData = Vue.ref([]);
        const popupDataType = Vue.ref(null);
        const popupLoadCount = 100;
        const popupPageNumber = Vue.ref(1);
        const popupTotalRecords = Vue.computed(() => {
            if(popupDataType.value){
                return uploadSummaryData.value[popupDataType.value];
            }
            else{
                return 0;
            }
        });
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processingStatusOptions = Vue.computed(() => baseStore.getOccurrenceProcessingStatusOptions);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const profileCleanSqlArr = Vue.computed(() => collectionDataUploadParametersStore.getCleanSqlArr);
        const profileConfigurationData = Vue.computed(() => collectionDataUploadParametersStore.getConfigurations);
        const profileData = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersData);
        const recordsUploadedDetermination = Vue.ref(0);
        const recordsUploadedMof = Vue.ref(0);
        const recordsUploadedMultimedia = Vue.ref(0);
        const recordsUploadedOccurrence = Vue.ref(0);
        const savedMappingDataDetermiation = Vue.ref({});
        const savedMappingDataMedia = Vue.ref({});
        const savedMappingDataMof = Vue.ref({});
        const savedMappingDataOccurrence = Vue.ref({});
        const scrollProcess = Vue.ref(null);
        const selectedProcessingStatus = Vue.ref(null);
        const showCollectionDataUploadParametersEditorPopup = Vue.ref(false);
        const showFieldMapperPopup = Vue.ref(false);
        const showUploadDataTableViewerPopup = Vue.ref(false);
        const skipDeterminationFields = ['updid','occid','collid','dbpk','tid','initialtimestamp'];
        const skipMediaFields = ['upmid','tid','occid','collid','dbpk','username','initialtimestamp'];
        const skipOccurrenceFields = ['upspid','occid','collid','dbpk','institutionid','collectionid','datasetid','tid',
            'eventid','eventdbpk','locationid','initialtimestamp'];
        const sourceDataFieldsDetermination = Vue.ref({});
        const sourceDataFieldsFlatFile = Vue.ref({});
        const sourceDataFieldsMof = Vue.ref({});
        const sourceDataFieldsMultimedia = Vue.ref({});
        const sourceDataFieldsOccurrence = Vue.ref({});
        const sourceDataFilesDetermination = Vue.ref([]);
        const sourceDataFilesMof = Vue.ref([]);
        const sourceDataFilesMultimedia = Vue.ref([]);
        const sourceDataFilesOccurrence = Vue.ref([]);
        const sourceDataFlatFile = Vue.ref([]);
        const sourceDataUploadCount = Vue.ref(0);
        const sourceDataUploadStage = Vue.ref(null);
        const symbiotaFieldOptionsDetermination = Vue.ref([]);
        const symbiotaFieldOptionsFlatFile = Vue.ref([]);
        const symbiotaFieldOptionsMedia = Vue.ref([]);
        const symbiotaFieldOptionsMof = Vue.ref([
            {value: 'unmapped', label: 'UNMAPPED'},
            {value: 'field', label: 'measurementtype'},
            {value: 'datavalue', label: 'measurementvalue'}
        ]);
        const symbiotaFieldOptionsOccurrence = Vue.ref([]);
        const uploadedFile = Vue.ref(null);
        const uploadSummaryData = Vue.ref({});
        
        function addProcessToProcessorDisplay(processObj) {
            processorDisplayArr.push(processObj);
            if(processorDisplayArr.length > 100){
                const precessorArrSegment = processorDisplayArr.slice(0, 100);
                processorDisplayDataArr = processorDisplayDataArr.concat(precessorArrSegment);
                processorDisplayArr.splice(0, 100);
                processorDisplayIndex.value++;
                processorDisplayCurrentIndex.value = processorDisplayIndex.value;
            }
        }

        function addSubprocessToProcessorDisplay(type, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            parentProcObj['subs'].push(getNewSubprocessObject(type, text));
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === currentProcess.value);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(getNewSubprocessObject(type, text));
            }
        }

        function adjustUIEnd() {
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
        }

        function adjustUIStart() {
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
        }

        function clearData() {
            flatFileMode.value = false;
            includeDeterminationData.value = true;
            includeMultimediaData.value = true;
            includeMofData.value = true;
            determinationDataIncluded.value = false;
            multimediaDataIncluded.value = false;
            mofDataIncluded.value = false;
            mofEventDataIncluded.value = false;
            mofOccurrenceDataIncluded.value = false;
            fieldMappingDataDetermiation.value = Object.assign({}, {});
            fieldMappingDataMedia.value = Object.assign({}, {});
            fieldMappingDataMof.value = Object.assign({}, {});
            fieldMappingDataOccurrence.value = Object.assign({}, {});
            flatFileMofData.value.length = 0;
            flatFileOccurrenceData.value.length = 0;
            savedMappingDataDetermiation.value = Object.assign({}, {});
            savedMappingDataMedia.value = Object.assign({}, {});
            savedMappingDataMof.value = Object.assign({}, {});
            savedMappingDataOccurrence.value = Object.assign({}, {});
            symbiotaFieldOptionsDetermination.value.length = 0;
            symbiotaFieldOptionsFlatFile.value.length = 0;
            symbiotaFieldOptionsMedia.value.length = 0;
            symbiotaFieldOptionsOccurrence.value.length = 0;
            symbiotaFieldOptionsDetermination.value.push({value: 'unmapped', label: 'UNMAPPED'});
            symbiotaFieldOptionsMedia.value.push({value: 'unmapped', label: 'UNMAPPED'});
            symbiotaFieldOptionsOccurrence.value.push({value: 'unmapped', label: 'UNMAPPED'});
            sourceDataFieldsDetermination.value = Object.assign({}, {});
            sourceDataFieldsFlatFile.value = Object.assign({}, {});
            sourceDataFieldsMof.value = Object.assign({}, {});
            sourceDataFieldsMultimedia.value = Object.assign({}, {});
            sourceDataFieldsOccurrence.value = Object.assign({}, {});
            sourceDataFilesDetermination.value.length = 0;
            sourceDataFilesMof.value.length = 0;
            sourceDataFilesMultimedia.value.length = 0;
            sourceDataFilesOccurrence.value.length = 0;
            sourceDataFlatFile.value.length = 0;
            recordsUploadedDetermination.value = 0;
            recordsUploadedMof.value = 0;
            recordsUploadedMultimedia.value = 0;
            recordsUploadedOccurrence.value = 0;
            sourceDataUploadCount.value = 0;
            sourceDataUploadStage.value = null;
        }

        function clearOccurrenceUploadTables() {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'clearOccurrenceUploadTables');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 0){
                    showNotification('negative', 'An error occurred while clearing the upload tables. Please contact the portal administrator before proceeding.');
                }
            });
        }

        function finalTransfer() {
            adjustUIStart();
            if(profileConfigurationData.value['existingRecords'] === 'skip'){
                finalTransferRemoveUnmatchedOccurrences();
            }
            else{
                finalTransferUpdateExistingOccurrences();
            }
        }

        function finalTransferAddNewDeterminations() {
            const text = 'Transferring new identification records';
            currentProcess.value = 'finalTransferAddNewDeterminations';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferAddNewDeterminations');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferProcessMedia();
                }
                else{
                    processErrorResponse('An error occurred while transferring new identification records');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferAddNewMedia() {
            const text = 'Transferring new media records';
            currentProcess.value = 'finalTransferAddNewMedia';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferAddNewMedia');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferProcessMof();
                }
                else{
                    processErrorResponse('An error occurred while transferring new media records');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferAddNewOccurrences() {
            const text = 'Transferring new occurrence records';
            currentProcess.value = 'finalTransferAddNewOccurrences';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferAddNewOccurrences');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferSetNewOccurrenceIds();
                }
                else{
                    processErrorResponse('An error occurred while transferring new occurrence records');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferCleanMediaRecords() {
            const text = 'Cleaning media records in upload';
            currentProcess.value = 'finalTransferCleanMediaRecords';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferCleanMediaRecords');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    if(profileConfigurationData.value['existingMediaRecords'] === 'merge'){
                        finalTransferRemoveExistingMediaRecordsFromUpload();
                    }
                    else{
                        finalTransferClearPreviousMediaRecords();
                    }
                }
                else{
                    processErrorResponse('An error occurred while cleaning media records in upload');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferClearPreviousDeterminations() {
            const text = 'Clearing previous determination records';
            currentProcess.value = 'finalTransferClearPreviousDeterminations';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferClearPreviousDeterminations');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferAddNewDeterminations();
                }
                else{
                    processErrorResponse('An error occurred while clearing previous determination records');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferClearPreviousMediaRecords() {
            const text = 'Clearing previous media records';
            currentProcess.value = 'finalTransferClearPreviousMediaRecords';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferClearPreviousMediaRecords');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferAddNewMedia();
                }
                else{
                    processErrorResponse('An error occurred while clearing previous media records');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferProcessDeterminations() {
            if(includeDeterminationData.value && Number(uploadSummaryData.value['ident']) > 0){
                if(profileConfigurationData.value['existingDeterminationRecords'] === 'merge'){
                    finalTransferRemoveExistingDeterminationsFromUpload();
                }
                else{
                    finalTransferClearPreviousDeterminations();
                }
            }
            else{
                finalTransferProcessMedia();
            }
        }

        function finalTransferProcessMedia() {
            if(includeMultimediaData.value && Number(uploadSummaryData.value['media']) > 0){
                finalTransferCleanMediaRecords();
            }
            else{
                finalTransferProcessMof();
            }
        }

        function finalTransferProcessMof() {
            if(includeMofData.value && Number(uploadSummaryData.value['mof']) > 0){
                if(profileConfigurationData.value['existingMediaRecords'] === 'merge'){
                    finalTransferRemoveExistingDeterminationsFromUpload();
                }
                else{
                    finalTransferClearPreviousDeterminations();
                }
            }
            else{

            }
        }

        function finalTransferRemoveExistingDeterminationsFromUpload() {
            const text = 'Removing existing determination records from upload';
            currentProcess.value = 'finalTransferRemoveExistingDeterminationsFromUpload';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferRemoveExistingDeterminationsFromUpload');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferAddNewDeterminations();
                }
                else{
                    processErrorResponse('An error occurred while removing existing determination records from upload');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferRemoveExistingMediaRecordsFromUpload() {
            const text = 'Removing existing media records from upload';
            currentProcess.value = 'finalTransferRemoveExistingMediaRecordsFromUpload';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferRemoveExistingMediaRecordsFromUpload');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferAddNewMedia();
                }
                else{
                    processErrorResponse('An error occurred while removing existing media records from upload');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferRemoveUnmatchedOccurrences() {
            if(profileConfigurationData.value['removeUnmatchedRecords'] && Number(uploadSummaryData.value['exist']) > 0){
                const text = 'Removing previous records not included in upload';
                currentProcess.value = 'finalTransferRemoveUnmatchedOccurrences';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('action', 'finalTransferRemoveUnmatchedOccurrences');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(Number(res) === 1){
                        processSuccessResponse('Complete');
                        finalTransferAddNewOccurrences();
                    }
                    else{
                        processErrorResponse('An error occurred while removing unmatched occurrence records');
                        adjustUIEnd();
                    }
                });
            }
            else{
                finalTransferAddNewOccurrences();
            }
        }

        function finalTransferSetNewOccurrenceIds() {
            const text = 'Populating IDs of new occurrence records in upload data';
            currentProcess.value = 'finalTransferSetNewOccurrenceIds';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('updateAssociatedData', '1');
            formData.append('action', 'linkExistingOccurrencesToUpload');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferProcessDeterminations();
                }
                else{
                    processErrorResponse('An error occurred while populating IDs');
                    adjustUIEnd();
                }
            });
        }

        function finalTransferUpdateExistingOccurrences() {
            const text = 'Updating existing occurrence records';
            currentProcess.value = 'finalTransferUpdateExistingOccurrences';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'finalTransferUpdateExistingOccurrences');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    finalTransferRemoveUnmatchedOccurrences();
                }
                else{
                    processErrorResponse('An error occurred while updating existing occurrence records');
                    adjustUIEnd();
                }
            });
        }

        function getFieldData() {
            const formData = new FormData();
            formData.append('tableArr', JSON.stringify(['uploaddetermtemp', 'uploadmediatemp', 'uploadspectemp']));
            formData.append('action', 'getUploadTableFieldData');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('uploaddetermtemp') && data['uploaddetermtemp'].length > 0){
                    data['uploaddetermtemp'].sort();
                    data['uploaddetermtemp'].forEach((field) => {
                        if(!skipDeterminationFields.includes(field)){
                            symbiotaFieldOptionsDetermination.value.push({value: field, label: field});
                        }
                    });
                }
                if(data.hasOwnProperty('uploadmediatemp') && data['uploadmediatemp'].length > 0){
                    data['uploadmediatemp'].sort();
                    data['uploadmediatemp'].forEach((field) => {
                        if(!skipMediaFields.includes(field)){
                            symbiotaFieldOptionsMedia.value.push({value: field, label: field});
                        }
                    });
                }
                if(data.hasOwnProperty('uploadspectemp') && data['uploadspectemp'].length > 0){
                    data['uploadspectemp'].sort();
                    data['uploadspectemp'].forEach((field) => {
                        if(!skipOccurrenceFields.includes(field)){
                            symbiotaFieldOptionsOccurrence.value.push({value: field, label: field});
                        }
                    });
                }
                processSuccessResponse('Complete');
                processSourceDataTransfer();
            });
        }

        function getFieldMapping() {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('uspid', collectionDataUploadParametersId.value.toString());
            formData.append('action', 'getUploadParametersFieldMapping');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.length > 0){
                    data.forEach((mapData) => {
                        if(mapData['symbspecfield'].startsWith('ID-')){
                            savedMappingDataDetermiation.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('IM-')){
                            savedMappingDataMedia.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('MOF-')){
                            savedMappingDataMof.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(4);
                        }
                        else{
                            savedMappingDataOccurrence.value[mapData['sourcefield']] = mapData['symbspecfield'];
                        }
                    });
                }
                getFieldData();
            });
        }

        function getNewProcessObject(type, text) {
            if(processorDisplayArr.length > 0){
                const pastProcObj = processorDisplayArr[(processorDisplayArr.length - 1)];
                if(pastProcObj){
                    pastProcObj['current'] = false;
                    if(pastProcObj.hasOwnProperty('subs') && pastProcObj['subs'].length > 0){
                        const subProcObj = pastProcObj['subs'][(pastProcObj['subs'].length - 1)];
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            if(!subProcObj['result'] || subProcObj['result'] === ''){
                                subProcObj['result'] = 'success';
                            }
                            if(!subProcObj['resultText'] || subProcObj['resultText'] === ''){
                                subProcObj['resultText'] = 'Complete';
                            }
                        }
                    }
                    else{
                        if(!pastProcObj['result'] || pastProcObj['result'] === ''){
                            pastProcObj['result'] = 'success';
                        }
                        if(!pastProcObj['resultText'] || pastProcObj['resultText'] === ''){
                            pastProcObj['resultText'] = 'Complete';
                        }
                    }
                }
            }
            const procObj = {
                id: currentProcess.value,
                procText: text,
                type: type,
                loading: true,
                current: true,
                result: '',
                resultText: ''
            };
            if(type === 'multi'){
                procObj['subs'] = [];
            }
            return procObj;
        }

        function getNewSubprocessObject(type, text) {
            return {
                procText: text,
                type: type,
                loading: true,
                result: '',
                resultText: ''
            };
        }

        function getPopupViewerRecords(pageNumber) {
            popupPageNumber.value = pageNumber;
            getUploadData(popupDataType.value, (data) => {
                popupData.value = data.slice();
            }, popupPageNumber.value, popupLoadCount);
        }

        function getUploadData(type, callback, index = null, limit = null) {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('dataType', type);
            if(Number(limit) > 0){
                formData.append('index', index.toString());
                formData.append('limit', limit.toString());
            }
            formData.append('action', 'getUploadData');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                callback(data);
            });
        }

        function getUploadSummary() {
            const text = 'Getting upload summary';
            currentProcess.value = 'gettingUploadSummary';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'getUploadSummary');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data){
                    uploadSummaryData.value = Object.assign({}, data);
                    processSuccessResponse('Complete');
                    currentTab.value = 'summary';
                    currentProcess.value = null;
                }
                else{
                    processErrorResponse('An error occurred while getting the upload summary');
                    adjustUIEnd();
                }
            });
        }

        function initializeUpload() {
            adjustUIStart();
            clearData();
            const text = 'Setting Symbiota field mapping data';
            currentProcess.value = 'setFieldMappingData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            if(Number(collectionDataUploadParametersId.value) > 0){
                getFieldMapping();
            }
            else{
                getFieldData();
            }
        }

        function openFieldMapperPopup(type) {
            fieldMapperPopupType.value = type;
            showFieldMapperPopup.value = true;
        }

        function parseFlatFileData() {
            const text = 'Processing data for upload';
            currentProcess.value = 'transferSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const idField = Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === 'dbpk');
            sourceDataFlatFile.value.forEach((dataRow) => {
                const occurrenceData = {};
                occurrenceData['dbpk'] = dataRow[idField];
                Object.keys(dataRow).forEach((field) => {
                    if(fieldMappingDataOccurrence.value.hasOwnProperty(field) && fieldMappingDataOccurrence.value[field] !== 'unmapped'){
                        if(eventMofDataFields.value.hasOwnProperty(fieldMappingDataOccurrence.value[field]) || occurrenceMofDataFields.value.hasOwnProperty(fieldMappingDataOccurrence.value[field])){
                            if(dataRow[field]){
                                const mofData = {};
                                mofData['dbpk'] = dataRow[idField];
                                mofData['field'] = fieldMappingDataOccurrence.value[field];
                                mofData['datavalue'] = dataRow[field];
                                flatFileMofData.value.push(mofData);
                            }
                        }
                        else{
                            occurrenceData[fieldMappingDataOccurrence.value[field]] = dataRow[field];
                        }
                    }
                });
                flatFileOccurrenceData.value.push(occurrenceData);
            });
            processSuccessResponse('Complete');
            processFlatFileSourceData();
        }

        function processDownloadRecords(type, filename) {
            const fullFilename = props.collid.toString() + '_' + filename + '.csv';
            showWorking();
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('dataType', type);
            formData.append('filename', fullFilename);
            formData.append('action', 'processUploadDataDownload');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.blob() : null;
            })
            .then((blob) => {
                hideWorking();
                if(blob !== null){
                    const objectUrl = window.URL.createObjectURL(blob);
                    const anchor = document.createElement('a');
                    anchor.href = objectUrl;
                    anchor.download = fullFilename;
                    document.body.appendChild(anchor);
                    anchor.click();
                    anchor.remove();
                }
            });
        }

        function processErrorResponse(text) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'error';
                    procObj['resultText'] = text;
                }
            }
        }

        function processFieldMapperUpdate(data) {
            if(fieldMapperPopupType.value === 'occurrence' || fieldMapperPopupType.value === 'flat-file'){
                fieldMappingDataOccurrence.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'determination'){
                fieldMappingDataDetermiation.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                fieldMappingDataMedia.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'mof'){
                fieldMappingDataMof.value[data['sourceField']] = data['targetField'];
            }
        }

        function processFlatFileCsvData(csvData) {
            if(csvData.length > 0){
                let generateCoreIds = false;
                csvData.forEach((dataObj, index) => {
                    if(index === 0){
                        if(!dataObj.hasOwnProperty('id') && !dataObj.hasOwnProperty('coreid')){
                            generateCoreIds = true;
                        }
                        if(generateCoreIds){
                            sourceDataFieldsFlatFile.value['id'] = 'coreid';
                        }
                        Object.keys(dataObj).forEach((key) => {
                            if(key.toLowerCase() === 'id' || key.toLowerCase() === 'coreid'){
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = 'coreid';
                            }
                            else if(key.toLowerCase() === 'eventid'){
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = 'coreeventid';
                            }
                            else{
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = key;
                            }
                        });
                    }
                    if(generateCoreIds){
                        dataObj['id'] = (index + 1).toString();
                    }
                    sourceDataFlatFile.value.push(dataObj);
                });
            }
            validateFieldMappingData();
        }

        function processFlatFileGeoJson(geojsonData) {
            let generateCoreIds = false;
            const geoJSONFormat = new ol.format.GeoJSON();
            const uploadFeatures = geoJSONFormat.readFeatures(geojsonData);
            uploadFeatures.forEach((feature, index) => {
                if(feature){
                    const featureData = {};
                    const featureProps = feature.getProperties();
                    const selectedClone = feature.clone();
                    const geoType = selectedClone.getGeometry().getType();
                    const featureGeometry = selectedClone.getGeometry();
                    if(!featureProps.hasOwnProperty('id') && !featureProps.hasOwnProperty('coreid')){
                        generateCoreIds = true;
                    }
                    if(generateCoreIds){
                        sourceDataFieldsFlatFile.value['id'] = 'coreid';
                    }
                    Object.keys(featureProps).forEach((prop) => {
                        if(prop !== 'geometry'){
                            if(prop.toLowerCase() === 'id' || prop.toLowerCase() === 'coreid'){
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = 'coreid';
                            }
                            else if(prop.toLowerCase() === 'eventid'){
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = 'coreeventid';
                            }
                            else{
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = prop;
                            }
                            if(featureProps[prop]){
                                featureData[prop.toLowerCase()] = isNaN(featureProps[prop]) ? featureProps[prop].trim() : featureProps[prop];
                            }
                            else{
                                featureData[prop.toLowerCase()] = null;
                            }
                        }
                    });
                    if(generateCoreIds){
                        featureData['id'] = (index + 1).toString();
                    }
                    if(geoType === 'Polygon' || geoType === 'MultiPolygon'){
                        const wktFormat = new ol.format.WKT();
                        featureData['footprintwkt'] = wktFormat.writeGeometry(featureGeometry);
                        sourceDataFieldsFlatFile.value['footprintwkt'] = 'footprintwkt';
                    }
                    else if((geoType === 'Point' || geoType === 'MultiPoint') && (!featureData.hasOwnProperty('decimallatitude') || !featureData.hasOwnProperty('decimallongitude') || !featureData['decimallatitude'] || !featureData['decimallongitude'])){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        const geojsonStr = geoJSONFormat.writeGeometry(featureGeometry);
                        const featCoords = geoType === 'Point' ? JSON.parse(geojsonStr).coordinates : JSON.parse(geojsonStr).coordinates[0];
                        featureData['decimallatitude'] = featCoords[1];
                        featureData['decimallongitude'] = featCoords[0];
                        sourceDataFieldsFlatFile.value['decimallatitude'] = 'decimallatitude';
                        sourceDataFieldsFlatFile.value['decimallongitude'] = 'decimallongitude';
                    }
                    sourceDataFlatFile.value.push(featureData);
                }
            });
            validateFieldMappingData();
        }

        function processFlatFileSourceData() {
            let data = [];
            let countChange = false;
            let currentComplete = false;
            let totalRecordsLoaded = 0;
            const configuration = {
                processingStatus: selectedProcessingStatus.value
            };
            if(flatFileOccurrenceData.value.length > 0){
                if(sourceDataUploadStage.value !== 'occurrence'){
                    countChange = true;
                    sourceDataUploadStage.value = 'occurrence';
                    const text = 'Loading occurrence data:';
                    currentProcess.value = 'transferSourceDataOccurrence';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                data = flatFileOccurrenceData.value.length > 500 ? flatFileOccurrenceData.value.slice(0, 500) : flatFileOccurrenceData.value.slice();
                configuration['dataType'] = 'occurrence';
                if(flatFileOccurrenceData.value.length > 500){
                    flatFileOccurrenceData.value.splice(0, 500);
                }
                else{
                    flatFileOccurrenceData.value.length = 0;
                }
                currentComplete = flatFileOccurrenceData.value.length === 0;
            }
            else if(flatFileMofData.value.length > 0){
                if(sourceDataUploadStage.value !== 'mof'){
                    countChange = true;
                    sourceDataUploadStage.value = 'mof';
                    const text = 'Loading measurement or fact data:';
                    currentProcess.value = 'transferSourceDataMof';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                mofDataIncluded.value = true;
                data = flatFileMofData.value.length > 500 ? flatFileMofData.value.slice(0, 500) : flatFileMofData.value.slice();
                configuration['dataType'] = 'mof';
                if(flatFileMofData.value.length > 500){
                    flatFileMofData.value.splice(0, 500);
                }
                else{
                    flatFileMofData.value.length = 0;
                }
                currentComplete = flatFileMofData.value.length === 0;
            }
            if(configuration.hasOwnProperty('dataType')){
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadConfig', JSON.stringify(configuration));
                formData.append('data', JSON.stringify(data));
                formData.append('action', 'processFlatFileDataUpload');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(!countChange && sourceDataUploadCount.value !== Number(res)){
                        countChange = true;
                    }
                    sourceDataUploadCount.value = Number(res);
                    let resText = '';
                    if(configuration['dataType'] === 'occurrence'){
                        recordsUploadedOccurrence.value = recordsUploadedOccurrence.value + Number(res);
                        totalRecordsLoaded = recordsUploadedOccurrence.value;
                        resText = Number(res) + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'mof'){
                        recordsUploadedMof.value = recordsUploadedMof.value + Number(res);
                        totalRecordsLoaded = recordsUploadedMof.value;
                        resText = Number(res) + ' records loaded'
                    }
                    if(countChange){
                        addSubprocessToProcessorDisplay('text', resText);
                        processSubprocessSuccessResponse(currentProcess.value, false);
                    }
                    if(currentComplete){
                        processSuccessResponse('Complete: ' + totalRecordsLoaded + ' total records loaded');
                    }
                    processFlatFileSourceData();
                });
            }
            else{
                processPostUploadMofFieldProcessing();
            }
        }

        function processOpenRecordViewerPopup(type) {
            popupData.value.length = 0;
            popupDataType.value = type;
            getPopupViewerRecords(1);
            showUploadDataTableViewerPopup.value = true;
        }

        function processorDisplayScrollDown() {
            scrollProcess.value = 'scrollDown';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value++;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processorDisplayScrollUp() {
            scrollProcess.value = 'scrollUp';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value--;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processParameterProfileSelection(uspid) {
            collectionDataUploadParametersStore.setCurrentCollectionDataUploadParametersRecord(uspid);
        }

        function processPostUploadCleanCoordinates() {
            const text = 'Cleaning coordinates';
            currentProcess.value = 'cleaningCoordinates';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'cleanUploadCoordinates');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                }
                else{
                    processErrorResponse('An error occurred while cleaning coordinates');
                }
                processPostUploadCleanTaxonomy();
            });
        }

        function processPostUploadCleanCountryStateNames() {
            const text = 'Cleaning country and state/province names';
            currentProcess.value = 'cleaningCountryState';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'cleanUploadCountryStateNames');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                }
                else{
                    processErrorResponse('An error occurred while cleaning country and state/province names');
                }
                processPostUploadCleanCoordinates();
            });
        }

        function processPostUploadCleanEventDates() {
            const text = 'Cleaning event dates';
            currentProcess.value = 'cleaningEventDates';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'cleanUploadEventDates');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                }
                else{
                    processErrorResponse('An error occurred while cleaning event dates');
                }
                processPostUploadCleanCountryStateNames();
            });
        }

        function processPostUploadCleaningScripts() {
            if(profileCleanSqlArr.value.length > 0){
                const text = 'Running configured cleaning scripts';
                currentProcess.value = 'runningCleaningScripts';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('cleaningScriptArr', JSON.stringify(profileCleanSqlArr.value));
                formData.append('action', 'executeCleaningScriptArr');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(Number(res) === 1){
                        processSuccessResponse('Complete');
                        processPostUploadCleanEventDates();
                    }
                    else{
                        processErrorResponse('An error occurred running cleaning scripts');
                        adjustUIEnd();
                    }
                });
            }
            else{
                processPostUploadCleanEventDates();
            }
        }

        function processPostUploadCleanTaxonomy() {
            const text = 'Cleaning taxonomy';
            currentProcess.value = 'cleaningTaxonomy';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'cleanUploadTaxonomy');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                }
                else{
                    processErrorResponse('An error occurred while cleaning taxonomy');
                }
                getUploadSummary();
            });
        }

        function processPostUploadExistingRecordProcessing() {
            let text;
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            if(profileConfigurationData.value['existingRecords'] === 'skip'){
                text = 'Removing existing occurrence data from upload';
                currentProcess.value = 'removeExistingOccurrences';
                formData.append('action', 'removeExistingOccurrencesFromUpload');
            }
            else{
                text = 'Associating upload data with existing occurrence records';
                currentProcess.value = 'linkExistingOccurrences';
                formData.append('action', 'linkExistingOccurrencesToUpload');
                if(profileConfigurationData.value['matchOnCatalogNumber']){
                    formData.append('matchByCatalogNumber', '1');
                    formData.append('linkField', profileConfigurationData.value['catalogNumberMatchField']);
                }
            }
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    processSuccessResponse('Complete');
                    processPostUploadCleaningScripts();
                }
                else{
                    processErrorResponse('An error occurred');
                    adjustUIEnd();
                }
            });
        }

        function processPostUploadMofFieldProcessing() {
            if(mofDataIncluded.value){
                const text = 'Analyzing uploaded measurement or fact data';
                currentProcess.value = 'analyzeUploadedMofData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('action', 'getUploadedMofDataFields');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(data.length > 0){
                        data.forEach((field) => {
                            if(eventMofDataFields.value.hasOwnProperty(field)){
                                mofEventDataIncluded.value = true;
                            }
                            else if(occurrenceMofDataFields.value.hasOwnProperty(field)){
                                mofOccurrenceDataIncluded.value = true;
                            }
                        });
                    }
                    processSuccessResponse('Complete');
                    processPostUploadExistingRecordProcessing();
                });
            }
            else{
                processPostUploadExistingRecordProcessing();
            }
        }

        function processSourceDataFiles() {
            let countChange = false;
            let currentComplete = false;
            let totalRecordsLoaded = 0;
            const configuration = {
                eventMofFields: eventMofDataFields.value,
                occurrenceMofFields: occurrenceMofDataFields.value,
                processingStatus: selectedProcessingStatus.value,
                serverPath: localDwcaServerPath.value
            };
            if(sourceDataFilesOccurrence.value.length > 0){
                if(sourceDataUploadStage.value !== 'occurrence'){
                    countChange = true;
                    sourceDataUploadStage.value = 'occurrence';
                    const text = 'Loading occurrence data:';
                    currentProcess.value = 'transferSourceDataOccurrence';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                configuration['uploadFile'] = sourceDataFilesOccurrence.value[0];
                configuration['dataType'] = 'occurrence';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataOccurrence.value);
                sourceDataFilesOccurrence.value.splice(0, 1);
                currentComplete = sourceDataFilesOccurrence.value.length === 0;
            }
            else if(includeDeterminationData.value && sourceDataFilesDetermination.value.length > 0){
                if(sourceDataUploadStage.value !== 'determination'){
                    countChange = true;
                    sourceDataUploadStage.value = 'determination';
                    const text = 'Loading determination data:';
                    currentProcess.value = 'transferSourceDataDetermination';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                configuration['uploadFile'] = sourceDataFilesDetermination.value[0];
                configuration['dataType'] = 'determination';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataDetermiation.value);
                sourceDataFilesDetermination.value.splice(0, 1);
                currentComplete = sourceDataFilesDetermination.value.length === 0;
            }
            else if(includeMultimediaData.value && sourceDataFilesMultimedia.value.length > 0){
                if(sourceDataUploadStage.value !== 'multimedia'){
                    countChange = true;
                    sourceDataUploadStage.value = 'multimedia';
                    const text = 'Loading media data:';
                    currentProcess.value = 'transferSourceDataMedia';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                configuration['uploadFile'] = sourceDataFilesMultimedia.value[0];
                configuration['dataType'] = 'multimedia';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataMedia.value);
                sourceDataFilesMultimedia.value.splice(0, 1);
                currentComplete = sourceDataFilesMultimedia.value.length === 0;
            }
            else if(includeMofData.value && sourceDataFilesMof.value.length > 0){
                if(sourceDataUploadStage.value !== 'mof'){
                    countChange = true;
                    sourceDataUploadStage.value = 'mof';
                    const text = 'Loading measurement or fact data:';
                    currentProcess.value = 'transferSourceDataMof';
                    addProcessToProcessorDisplay(getNewProcessObject('multi', text));
                }
                configuration['uploadFile'] = sourceDataFilesMof.value[0];
                configuration['dataType'] = 'mof';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataMof.value);
                sourceDataFilesMof.value.splice(0, 1);
                currentComplete = sourceDataFilesMof.value.length === 0;
            }
            if(configuration.hasOwnProperty('dataType')){
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadConfig', JSON.stringify(configuration));
                formData.append('action', 'processDwcaFileDataUpload');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(!countChange && sourceDataUploadCount.value !== Number(res)){
                        countChange = true;
                    }
                    sourceDataUploadCount.value = Number(res);
                    let resText = '';
                    if(configuration['dataType'] === 'occurrence'){
                        recordsUploadedOccurrence.value = recordsUploadedOccurrence.value + Number(res);
                        totalRecordsLoaded = recordsUploadedOccurrence.value;
                        resText = Number(res) + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'determination'){
                        recordsUploadedDetermination.value = recordsUploadedDetermination.value + Number(res);
                        totalRecordsLoaded = recordsUploadedDetermination.value;
                        resText = Number(res) + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'multimedia'){
                        recordsUploadedMultimedia.value = recordsUploadedMultimedia.value + Number(res);
                        totalRecordsLoaded = recordsUploadedMultimedia.value;
                        resText = Number(res) + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'mof'){
                        recordsUploadedMof.value = recordsUploadedMof.value + Number(res);
                        totalRecordsLoaded = recordsUploadedMof.value;
                        resText = Number(res) + ' records loaded'
                    }
                    if(countChange){
                        addSubprocessToProcessorDisplay('text', resText);
                        processSubprocessSuccessResponse(currentProcess.value, false);
                    }
                    if(currentComplete){
                        processSuccessResponse('Complete: ' + totalRecordsLoaded + ' total records loaded');
                    }
                    processSourceDataFiles();
                });
            }
            else{
                processPostUploadMofFieldProcessing();
            }
        }

        function processSourceDataProcessing(metaFile) {
            const text = 'Processing source data';
            currentProcess.value = 'processSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('serverPath', localDwcaServerPath.value);
            formData.append('metaFile', metaFile.toString());
            formData.append('action', 'processTransferredDwca');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('occurrence') && data['occurrence']['dataFiles'].length > 0){
                    sourceDataFieldsOccurrence.value = Object.assign({}, data['occurrence']['fields']);
                    sourceDataFilesOccurrence.value = data['occurrence']['dataFiles'].slice();
                    if(data.hasOwnProperty('identification') && data['identification']['dataFiles'].length > 0){
                        sourceDataFieldsDetermination.value = Object.assign({}, data['identification']['fields']);
                        sourceDataFilesDetermination.value = data['identification']['dataFiles'].slice();
                        determinationDataIncluded.value = true;
                    }
                    if(data.hasOwnProperty('multimedia') && data['multimedia']['dataFiles'].length > 0){
                        sourceDataFieldsMultimedia.value = Object.assign({}, data['multimedia']['fields']);
                        sourceDataFilesMultimedia.value = data['multimedia']['dataFiles'].slice();
                        multimediaDataIncluded.value = true;
                    }
                    if(data.hasOwnProperty('measurementorfact') && data['measurementorfact']['dataFiles'].length > 0){
                        sourceDataFieldsMof.value = Object.assign({}, data['measurementorfact']['fields']);
                        sourceDataFilesMof.value = data['measurementorfact']['dataFiles'].slice();
                        mofDataIncluded.value = true;
                    }
                }
                validateFieldMappingData();
            });
        }

        function processSourceDataTransfer() {
            if(Number(profileData.value['uploadtype']) === 8 || Number(profileData.value['uploadtype']) === 10){
                const text = 'Transferring source data archive';
                currentProcess.value = 'transferSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadType', profileData.value['uploadtype'].toString());
                formData.append('dwcaPath', profileData.value['dwcpath'].toString());
                formData.append('action', 'processExternalDwcaTransfer');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(data && data.hasOwnProperty('targetPath') && data.hasOwnProperty('archivePath')){
                        processSuccessResponse('Complete');
                        processSourceDataUnpacking(data['targetPath'], data['archivePath']);
                    }
                    else{
                        processErrorResponse('The source data archive could not be transferred.');
                    }
                });
            }
            else if(Number(profileData.value['uploadtype']) === 6){
                processUploadFile();
            }
        }

        function processSourceDataUnpacking(targetPath, archivePath) {
            const text = 'Unpacking source data archive';
            currentProcess.value = 'unpackSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('targetPath', targetPath.toString());
            formData.append('archivePath', archivePath.toString());
            formData.append('action', 'processExternalDwcaUnpack');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                processSuccessResponse('Complete');
                localDwcaServerPath.value = data['baseFolderPath'];
                localDwcaFileArr.value = data['files'].slice();
                const metaFile = localDwcaFileArr.value.find(filename => filename.toLowerCase() === 'meta.xml');
                if(metaFile){
                    processSourceDataProcessing(metaFile);
                }
                else{
                    showNotification('negative', 'The Darwin Core Archive does not contain a meta.xml file, which is necessary for upload processing.');
                }
            });
        }

        function processSubprocessErrorResponse(text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            if(parentProcObj){
                parentProcObj['current'] = false;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'error';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSubprocessSuccessResponse(id, complete, text = null) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
            if(parentProcObj){
                parentProcObj['current'] = !complete;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'success';
                    subProcObj['resultText'] = text;
                }
            }
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
            if(dataParentProcObj){
                dataParentProcObj['current'] = !complete;
                const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(dataSubProcObj){
                    dataSubProcObj['loading'] = false;
                    dataSubProcObj['result'] = 'success';
                    dataSubProcObj['resultText'] = text;
                }
            }
        }

        function processSuccessResponse(text = null) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                }
                if(text){
                    if(procObj.hasOwnProperty('subs') && procObj['subs'].length > 0){
                        const subProcObj = procObj['subs'][(procObj['subs'].length - 1)];
                        if(subProcObj){
                            subProcObj['resultText'] = text;
                        }
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === procObj['id']);
                        if(dataParentProcObj){
                            dataParentProcObj['current'] = !complete;
                            const dataSubProcObj = dataParentProcObj['subs'][(dataParentProcObj['subs'].length - 1)];
                            if(dataSubProcObj){
                                dataSubProcObj['resultText'] = text;
                            }
                        }
                    }
                    else{
                        procObj['resultText'] = text;
                    }
                }
            }
        }

        function processUploadFile() {
            if(uploadedFile.value.name.endsWith('.zip')){
                const fileSizeMb = Number(uploadedFile.value.size) > 0 ? Math.round((uploadedFile.value.size / 1000000) * 10) / 100 : 0;
                if(fileSizeMb <= Number(maxUploadFilesize)){
                    transferUploadedDwcaFileToServer();
                }
                else{
                    showNotification('negative', (uploadedFile.value.name + ' cannot be uploaded because it is ' + fileSizeMb.toString() + 'MB, which exceeds the server limit of ' + maxUploadFilesize.toString() + 'MB for uploads.'));
                }
            }
            else if(uploadedFile.value.name.endsWith('.csv')){
                const text = 'Processing source data';
                currentProcess.value = 'processSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                flatFileMode.value = true;
                setSymbiotaFlatFileFieldOptions();
                parseCsvFile(uploadedFile.value, (csvData) => {
                    processFlatFileCsvData(csvData);
                });
            }
            else{
                const text = 'Processing source data';
                currentProcess.value = 'processSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                flatFileMode.value = true;
                setSymbiotaFlatFileFieldOptions();
                const fileReader = new FileReader();
                fileReader.onload = () => {
                    const uploadData = JSON.parse(fileReader.result);
                    processFlatFileGeoJson(uploadData);
                };
                fileReader.readAsText(uploadedFile.value);
            }
        }

        function resetScrollProcess() {
            setTimeout(() => {
                scrollProcess.value = null;
            }, 200);
        }

        function saveMapping() {
            const saveMappingData = {};
            saveMappingData['occurrence'] = {};
            saveMappingData['determination'] = {};
            saveMappingData['multimedia'] = {};
            saveMappingData['mof'] = {};
            Object.keys(fieldMappingDataOccurrence.value).forEach((field) => {
                const fieldName = flatFileMode.value ? sourceDataFieldsFlatFile.value[field] : sourceDataFieldsOccurrence.value[field];
                if(fieldName){
                    saveMappingData['occurrence'][fieldName.toLowerCase()] = fieldMappingDataOccurrence.value[field];
                }
            });
            Object.keys(fieldMappingDataDetermiation.value).forEach((field) => {
                const fieldName = sourceDataFieldsDetermination.value[field];
                if(fieldName){
                    saveMappingData['determination'][fieldName.toLowerCase()] = 'ID-' + fieldMappingDataDetermiation.value[field];
                }
            });
            Object.keys(fieldMappingDataMedia.value).forEach((field) => {
                const fieldName = sourceDataFieldsMultimedia.value[field];
                if(fieldName){
                    saveMappingData['multimedia'][fieldName.toLowerCase()] = 'IM-' + fieldMappingDataMedia.value[field];
                }
            });
            Object.keys(fieldMappingDataMof.value).forEach((field) => {
                const fieldName = sourceDataFieldsMof.value[field];
                if(fieldName){
                    saveMappingData['mof'][fieldName.toLowerCase()] = 'MOF-' + fieldMappingDataMof.value[field];
                }
            });
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('uspid', collectionDataUploadParametersId.value.toString());
            formData.append('fieldMappingData', JSON.stringify(saveMappingData));
            formData.append('action', 'saveFieldMapping');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    showNotification('positive','Field mapping saved.');
                }
                else{
                    showNotification('negative','An error occurred while saving the field mapping.');
                }
            });
        }

        function setScroller(info) {
            if((currentProcess.value || scrollProcess.value) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                procDisplayScrollHeight.value = info.verticalSize;
                if(scrollProcess.value === 'scrollDown'){
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                }
                else{
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                }
            }
        }

        function setSymbiotaFlatFileFieldOptions() {
            symbiotaFieldOptionsOccurrence.value.forEach((fieldOption) => {
                symbiotaFieldOptionsFlatFile.value.push(fieldOption);
            });
            Object.keys(eventMofDataFields.value).forEach((key) => {
                symbiotaFieldOptionsFlatFile.value.push({value: key, label: eventMofDataFields.value[key]['label']});
            });
            Object.keys(occurrenceMofDataFields.value).forEach((key) => {
                symbiotaFieldOptionsFlatFile.value.push({value: key, label: occurrenceMofDataFields.value[key]['label']});
            });
        }

        function startUpload() {
            adjustUIStart();
            if(flatFileMode.value){
                parseFlatFileData();
            }
            else{
                processSourceDataFiles();
            }
        }

        function transferUploadedDwcaFileToServer() {
            const text = 'Transferring source data';
            currentProcess.value = 'transferSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('dwcaFile', uploadedFile.value);
            formData.append('action', 'uploadDwcaFile');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                processSuccessResponse('Complete');
                localDwcaServerPath.value = data['baseFolderPath'];
                localDwcaFileArr.value = data['files'].slice();
                const metaFile = localDwcaFileArr.value.find(filename => filename.toLowerCase() === 'meta.xml');
                if(metaFile){
                    processSourceDataProcessing(metaFile);
                }
                else{
                    showNotification('negative', 'The Darwin Core Archive does not contain a meta.xml file, which is necessary for upload processing.');
                }
            });
        }

        function validateFieldMappingData() {
            let validated = true;
            if(flatFileMode.value && sourceDataFlatFile.value.length > 0 && Object.keys(sourceDataFieldsFlatFile.value).length > 0){
                Object.keys(sourceDataFieldsFlatFile.value).forEach((field) => {
                    const fieldName = sourceDataFieldsFlatFile.value[field];
                    if(!fieldMappingDataOccurrence.value.hasOwnProperty(fieldName.toLowerCase())){
                        if(fieldName === 'coreid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'dbpk';
                        }
                        else if(fieldName === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsFlatFile.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                            const usedField = fieldOption ? Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === fieldOption.value) : null;
                            fieldMappingDataOccurrence.value[fieldName.toLowerCase()] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                        }
                    }
                });
            }
            else if(sourceDataFilesOccurrence.value.length > 0 && Object.keys(sourceDataFieldsOccurrence.value).length > 0){
                Object.keys(sourceDataFieldsOccurrence.value).forEach((field) => {
                    const fieldName = sourceDataFieldsOccurrence.value[field];
                    if(Object.keys(savedMappingDataOccurrence.value).length === 0 || !savedMappingDataOccurrence.value.hasOwnProperty(fieldName.toLowerCase())){
                        if(fieldName === 'coreid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'dbpk';
                        }
                        else if(fieldName === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsOccurrence.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                            const usedField = fieldOption ? Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === fieldOption.value) : null;
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                        }
                    }
                    else{
                        const fieldOption = symbiotaFieldOptionsOccurrence.value.find(option => option.value.toLowerCase() === savedMappingDataOccurrence.value[fieldName.toLowerCase()]);
                        fieldMappingDataOccurrence.value[field.toLowerCase()] = fieldOption ? fieldOption.value : 'unmapped';
                    }
                });
                if(sourceDataFilesDetermination.value.length > 0 && Object.keys(sourceDataFieldsDetermination.value).length > 0){
                    Object.keys(sourceDataFieldsDetermination.value).forEach((field) => {
                        const fieldName = sourceDataFieldsDetermination.value[field];
                        if(Object.keys(savedMappingDataDetermiation.value).length === 0 || !savedMappingDataDetermiation.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataDetermiation.value[field.toLowerCase()] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsDetermination.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                                const usedField = fieldOption ? Object.keys(fieldMappingDataDetermiation.value).find(field => fieldMappingDataDetermiation.value[field] === fieldOption.value) : null;
                                fieldMappingDataDetermiation.value[field] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsDetermination.value.find(option => option.value.toLowerCase() === savedMappingDataDetermiation.value[fieldName.toLowerCase()]);
                            fieldMappingDataDetermiation.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
                if(sourceDataFilesMultimedia.value.length > 0 && Object.keys(sourceDataFieldsMultimedia.value).length > 0){
                    Object.keys(sourceDataFieldsMultimedia.value).forEach((field) => {
                        const fieldName = sourceDataFieldsMultimedia.value[field];
                        if(Object.keys(savedMappingDataMedia.value).length === 0 || !savedMappingDataMedia.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataMedia.value[field.toLowerCase()] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsMedia.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                                const usedField = fieldOption ? Object.keys(fieldMappingDataMedia.value).find(field => fieldMappingDataMedia.value[field] === fieldOption.value) : null;
                                fieldMappingDataMedia.value[field] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsMedia.value.find(option => option.value.toLowerCase() === savedMappingDataMedia.value[fieldName.toLowerCase()]);
                            fieldMappingDataMedia.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
                if(sourceDataFilesMof.value.length > 0 && Object.keys(sourceDataFieldsMof.value).length > 0){
                    Object.keys(sourceDataFieldsMof.value).forEach((field) => {
                        const fieldName = sourceDataFieldsMof.value[field];
                        if(Object.keys(savedMappingDataMof.value).length === 0 || !savedMappingDataMof.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataMof.value[field.toLowerCase()] = 'dbpk';
                            }
                            else if(fieldName === 'coreeventid'){
                                fieldMappingDataMof.value[field.toLowerCase()] = 'eventdbpk';
                            }
                            else if(fieldName.toLowerCase() === 'measurementtype'){
                                fieldMappingDataMof.value[field] = 'field';
                            }
                            else if(fieldName.toLowerCase() === 'measurementvalue'){
                                fieldMappingDataMof.value[field] = 'datavalue';
                            }
                            else{
                                fieldMappingDataMof.value[field] = 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsMof.value.find(option => option.value.toLowerCase() === savedMappingDataMof.value[fieldName.toLowerCase()]);
                            fieldMappingDataMof.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
            }
            else{
                validated = false;
            }
            processSuccessResponse('Complete');
            if(validated){
                currentTab.value = 'mapping';
                currentProcess.value = null;
            }
            else{
                showNotification('negative', 'Source data could not be correctly read to process upload.');
                adjustUIEnd();
            }
        }

        Vue.onMounted(() => {
            if(Number(props.collid) > 0){
                clearOccurrenceUploadTables();
                collectionDataUploadParametersStore.setCollectionDataUploadParametersArr(props.collid);
            }
        });

        return {
            acceptedFileTypes,
            collectionDataUploadParametersArr,
            collectionDataUploadParametersId,
            currentProcess,
            currentTab,
            determinationDataIncluded,
            fieldMapperFieldMapping,
            fieldMapperSourceFields,
            fieldMapperTargetFields,
            flatFileMode,
            includeDeterminationData,
            includeMultimediaData,
            includeMofData,
            multimediaDataIncluded,
            mofDataIncluded,
            popupColumns,
            popupData,
            popupLoadCount,
            popupPageNumber,
            popupTotalRecords,
            procDisplayScrollAreaRef,
            processingStatusOptions,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            profileData,
            selectedProcessingStatus,
            showCollectionDataUploadParametersEditorPopup,
            showFieldMapperPopup,
            showUploadDataTableViewerPopup,
            sourceDataFilesDetermination,
            sourceDataFilesMof,
            sourceDataFilesMultimedia,
            uploadedFile,
            uploadSummaryData,
            finalTransfer,
            getPopupViewerRecords,
            initializeUpload,
            openFieldMapperPopup,
            processDownloadRecords,
            processFieldMapperUpdate,
            processOpenRecordViewerPopup,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            processParameterProfileSelection,
            processUploadFile,
            saveMapping,
            startUpload
        }
    }
};
