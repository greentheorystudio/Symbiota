const occurrenceEditorChecklistVoucherModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section>
                <div class="row justify-between q-gutter-sm">
                    <div class="text-h6 text-bold">Checklist Voucher Linkages</div>
                </div>
                <template v-if="userChecklistArr.length > 0">
                    <q-card flat bordered>
                        <q-card-section class="full-width column q-gutter-xs q-pa-sm q-ma-none">
                            <div class="text-body1 text-bold">
                                Add a checklist voucher linkage
                            </div>
                            <div class="row justify-between q-gutter-sm">
                                <div class="col-grow">
                                    <selector-input-element :options="checklistOptions" label="Checklist" :value="selectedChecklist" option-label="name" option-value="clid" :clearable="true" @update:value="(value) => selectedChecklist = value"></selector-input-element>
                                </div>
                                <div>
                                    <q-btn color="primary" @click="linkVoucher();" label="Link Voucher" dense />
                                </div>
                            </div>
                            <div class="col-2">
                            
                            </div>
                        </q-card-section>
                    </q-card>
                </template>
                <div class="q-mt-xs">
                    <template v-if="voucherChecklistArr.length > 0">
                        <div class="q-pl-sm column q-gutter-sm">
                            <q-list dense>
                                <template v-for="checklist in voucherChecklistArr">
                                    <q-item>
                                        <q-item-section>
                                            <div class="row justify-start q-gutter-md items-center">
                                                <div class="text-body1">
                                                    <a :href="(clientRoot + '/checklists/checklist.php?cl=' + checklist.clid)" target="_blank">
                                                        {{ checklist.name }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <q-btn color="white" text-color="black" size=".6rem" @click="removeOccurrenceVoucherLinkage(checklist.clid);" icon="far fa-trash-alt" dense>
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Remove voucher linkage
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-list>
                        </div>
                    </template>
                    <template v-else>
                        <span class="text-body1 text-bold">There are no checklist voucher linkages for this record.</span>
                    </template>
                </div>
            </q-card-section>
        </q-card>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'selector-input-element': selectorInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const checklistOptions = Vue.ref([]);
        const confirmationPopupRef = Vue.ref(null);
        const clientRoot = baseStore.getClientRoot;
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const selectedChecklist = Vue.ref(null);
        const userChecklistArr = Vue.ref([]);
        const voucherChecklistArr = Vue.computed(() => occurrenceStore.getChecklistArr);

        Vue.watch(voucherChecklistArr, () => {
            setChecklistOptions();
        });

        function linkVoucher() {
            const formData = new FormData();
            formData.append('clid', selectedChecklist.value.toString());
            formData.append('occid', occId.value.toString());
            formData.append('tid', occurrenceData.value['tid'].toString());
            formData.append('action', 'addOccurrenceVoucherLinkage');
            fetch(checklistVoucherApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(Number(res) > 0){
                        occurrenceStore.setChecklistArr();
                        selectedChecklist.value = null;
                    }
                    else{
                        showNotification('negative', ('An error occurred while linking this voucher.'));
                    }
                });
            });
        }

        function removeOccurrenceVoucherLinkage(clid) {
            const confirmText = 'Are you sure you want to remove this occurrence as a voucher?';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'Cancel', trueText: 'Yes', callback: (val) => {
                if(val){
                    const formData = new FormData();
                    formData.append('collid', collId.value.toString());
                    formData.append('clid', clid.toString());
                    formData.append('occid', occId.value.toString());
                    formData.append('action', 'removeOccurrenceVoucherLinkage');
                    fetch(checklistVoucherApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.text().then((res) => {
                            if(Number(res) === 1){
                                occurrenceStore.setChecklistArr();
                            }
                            else{
                                showNotification('negative', ('An error occurred while removing this checklist voucher.'));
                            }
                        });
                    });
                }
            }});
        }

        function setChecklistOptions() {
            checklistOptions.value.length = 0;
            if(voucherChecklistArr.value.length > 0 && userChecklistArr.value.length > 0){
                userChecklistArr.value.forEach((checklist) => {
                    const voucherObj = voucherChecklistArr.value.find(voucher => Number(voucher['clid']) === Number(checklist['clid']));
                    if(!voucherObj){
                        checklistOptions.value.push(checklist);
                    }
                });
            }
            else{
                checklistOptions.value = userChecklistArr.value;
            }
        }

        function setAccountChecklists() {
            const formData = new FormData();
            formData.append('action', 'getChecklistListByUserRights');
            fetch(checklistApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    userChecklistArr.value = resObj;
                    setChecklistOptions();
                });
            });
        }

        Vue.onMounted(() => {
            setAccountChecklists();
        });

        return {
            checklistOptions,
            confirmationPopupRef,
            clientRoot,
            selectedChecklist,
            userChecklistArr,
            voucherChecklistArr,
            linkVoucher,
            removeOccurrenceVoucherLinkage
        }
    }
};
