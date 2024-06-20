const occurrenceEditorChecklistVoucherModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section>
                <div class="row justify-between q-gutter-sm">
                    <div class="text-h6 text-bold">Checklist Voucher Linkages</div>
                    <div>
                        <q-btn color="secondary" @click="" label="Add New Checklist Voucher Linkage" />
                    </div>
                </div>
                <div class="q-mt-xs q-pl-sm column q-gutter-sm">
                    
                </div>
            </q-card-section>
        </q-card>
    `,
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const userChecklistArr = Vue.ref([]);
        const voucherChecklistArr = Vue.computed(() => occurrenceStore.getChecklistArr);

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
                });
            });
        }

        Vue.onMounted(() => {
            setAccountChecklists();
        });

        return {

        }
    }
};
