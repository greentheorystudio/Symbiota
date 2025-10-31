const occurrenceEditorAdminTab = {
    template: `
        <div class="column q-gutter-sm">
            <q-card v-if="editArr.length > 0" flat bordered class="black-border">
                <q-card-section>
                    <div class="text-h6 text-bold">Edit History</div>
                    <div class="q-mt-xs q-pl-sm column q-col-gutter-sm">
                        <div class="text-bold">
                            <a :href="(clientRoot + '/collections/editor/editreviewer.php?collid=' + collId + '&occid=' + occId)" target="_blank" aria-label="Manage Edits - Opens in separate tab" tabindex="0">
                                Manage Edits
                            </a>
                        </div>
                        <template v-for="edit in editArr">
                            <div class="column">
                                <div class="row justify-start q-gutter-sm">
                                    <div>
                                        <span class="text-bold q-mr-sm">Editor:</span>{{ edit['editor'] }}
                                    </div>
                                    <div>
                                        <span class="text-bold q-mr-sm">Date:</span>{{ edit['ts'] }}
                                    </div>
                                    <div>
                                        <span class="text-bold q-mr-sm">Applied Status:</span>{{ Number(edit['appliedstatus']) === 1 ? 'applied' : 'not applied' }}
                                    </div>
                                    <div>
                                        <span class="text-bold q-mr-sm">Review Status:</span>{{ getReviewStatusText(edit['reviewstatus']) }}
                                    </div>
                                </div>
                                <div class="row justify-start q-gutter-sm">
                                    <div>
                                        <span class="text-bold q-mr-sm">Field:</span>{{ edit['fieldname'] }}
                                    </div>
                                    <div>
                                        <span class="text-bold q-mr-sm">Old Value:</span>{{ (edit['old'] && edit['old'] !== '') ? edit['old'] : '[NULL]' }}
                                    </div>
                                    <div>
                                        <span class="text-bold q-mr-sm">New Value:</span>{{ (edit['new'] && edit['new'] !== '') ? edit['new'] : '[NULL]' }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </q-card-section>
            </q-card>
            <q-card v-if="profileCollectionOptions.length > 0 && occurrenceEntryFormat !== 'benthic' && occurrenceEntryFormat !== 'lot'" flat bordered class="black-border">
                <q-card-section>
                    <div class="text-h6 text-bold">Transfer Record</div>
                    <div class="q-mt-xs q-pl-sm row justify-between">
                        <div class="col-6">
                            <selector-input-element 
                                :options="profileCollectionOptions" 
                                label="Transfer To Collection" 
                                :value="transferToCollid" 
                                :clearable="true" 
                                @update:value="updateTransferToCollid"
                            ></selector-input-element>
                        </div>
                        <div class="row justify-end">
                            <q-btn color="secondary" @click="transferOccurrenceRecord();" label="Transfer" :disabled="!transferToCollid" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered class="black-border">
                <q-card-section>
                    <div class="text-h6 text-bold">Delete Record</div>
                    <div class="q-mt-xs q-pl-sm column q-col-gutter-sm">
                        <div v-if="!occurrenceDeleteApproved" class="column q-gutter-sm">
                            <div class="text-bold">
                                This occurrence record has the following associations and therefore cannot be deleted. Remove these
                                associations through the tabs above in order to delete this record.
                            </div>
                            <div v-if="checklistArr.length > 0" class="q-ml-lg">
                                <span class="text-bold">Checklists: </span>This occurrence has voucher associations on {{ checklistArr.length + (checklistArr.length === 1 ? ' checklist' : ' checklists') }}.
                            </div>
                            <div v-if="geneticLinkArr.length > 0" class="q-ml-lg">
                                <span class="text-bold">Genetic Records: </span>This occurrence has {{ geneticLinkArr.length + (geneticLinkArr.length === 1 ? ' genetic record ' : ' genetic records ') }}associated with it.
                            </div>
                            <div v-if="imageArr.length > 0" class="q-ml-lg">
                                <span class="text-bold">Images: </span>This occurrence has {{ imageArr.length + (imageArr.length === 1 ? ' image ' : ' images ') }}associated with it.
                            </div>
                            <div v-if="mediaArr.length > 0" class="q-ml-lg">
                                <span class="text-bold">Media Files: </span>This occurrence has {{ mediaArr.length + (mediaArr.length === 1 ? ' media file ' : ' media files ') }}associated with it.
                            </div>
                        </div>
                        <div class="q-mt-md row justify-start">
                            <q-btn color="secondary" @click="processDeleteOccurrenceRecord();" label="Delete Occurrence" :disabled="!occurrenceDeleteApproved" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'selector-input-element': selectorInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const collectionStore = useCollectionStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const checklistArr = Vue.computed(() => occurrenceStore.getChecklistArr);
        const clientRoot = baseStore.getClientRoot;
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const confirmationPopupRef = Vue.ref(null);
        const editArr = Vue.computed(() => occurrenceStore.getEditArr);
        const geneticLinkArr = Vue.computed(() => occurrenceStore.getGeneticLinkArr);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);
        const mediaArr = Vue.computed(() => occurrenceStore.getMediaArr);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceDeleteApproved = Vue.computed(() => {
            return (
                checklistArr.value.length === 0 &&
                geneticLinkArr.value.length === 0 &&
                imageArr.value.length === 0 &&
                mediaArr.value.length === 0
            );
        });
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const profileCollectionOptions = Vue.ref([]);
        const symbUid = baseStore.getSymbUid;
        const transferToCollid = Vue.ref(null);

        function getReviewStatusText(statusCode) {
            let text = 'OPEN';
            if(Number(statusCode) === 2){
                text = 'PENDING';
            }
            else if(Number(statusCode) === 3){
                text = 'CLOSED';
            }
            return text;
        }

        function processDeleteOccurrenceRecord() {
            const confirmText = 'Are you sure you want to delete this record? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    occurrenceStore.deleteOccurrenceRecord(occId.value, (res) => {
                        if(res === 0){
                            showNotification('negative', ('An error occurred while deleting this record.'));
                        }
                        else{
                            searchStore.removeOccidFromOccidArrs(occId.value);
                            occurrenceStore.setCurrentOccurrenceRecord(searchStore.getPreviousOccidInOccidArr);
                        }
                    });
                }
            }});
        }

        function setProfileCollectionsOptions() {
            collectionStore.getCollectionListByUid(symbUid, (collListData) => {
                if(collListData.length > 0){
                    collListData.forEach(coll => {
                        if(Number(coll.collid) !== Number(collId.value)){
                            profileCollectionOptions.value.push({
                                value: coll.collid,
                                label: coll.label
                            });
                        }
                    });
                }
            });
        }

        function transferOccurrenceRecord() {
            occurrenceStore.transferOccurrenceRecord(transferToCollid.value, (res) => {
                if(Number(res) === 1){
                    showNotification('positive','Record was transferred successfully.');
                }
                else{
                    showNotification('negative', 'An error occurred while transferring the record.');
                }
            });
        }

        function updateTransferToCollid(value) {
            transferToCollid.value = value;
        }

        Vue.onMounted(() => {
            setProfileCollectionsOptions();
        });

        return {
            checklistArr,
            clientRoot,
            collId,
            confirmationPopupRef,
            editArr,
            geneticLinkArr,
            imageArr,
            mediaArr,
            occId,
            occurrenceDeleteApproved,
            occurrenceEntryFormat,
            profileCollectionOptions,
            transferToCollid,
            getReviewStatusText,
            processDeleteOccurrenceRecord,
            transferOccurrenceRecord,
            updateTransferToCollid
        }
    }
};
