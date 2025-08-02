const checklistTaxaVoucherModule = {
    template: `
        <div class="fit q-pa-md column q-gutter-sm no-wrap">
            <div class="row justify-end">
                <q-btn color="primary" @click="openOccurrenceLinkagePopup();" label="Add Vouchers" dense>
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Link occurrence voucher records for this taxon
                    </q-tooltip>
                </q-btn>
            </div>
            <template v-if="checklistTaxaVoucherArr.length > 0">
                <div class="q-pa-xs column q-gutter-sm">
                    <q-card v-for="voucher in checklistTaxaVoucherArr">
                        <q-card-section class="row justify-between q-col-gutter-sm no-wrap">
                            <occurrence-selector-info-block :occurrence-data="voucher"></occurrence-selector-info-block>
                            <div>
                                <q-btn color="negative" @click="deleteChecklistVoucherRecord(voucher['occid']);" label="Remove" dense />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </template>
            <template v-else>
                <div class="col-grow column justify-center">
                    <div class="text-body1 text-bold text-center">No voucher records have been linked for this taxon</div>
                </div>
            </template>
        </div>
    `,
    components: {
        'occurrence-selector-info-block': occurrenceSelectorInfoBlock
    },
    setup(_, context) {
        const { showNotification } = useCore();
        const checklistStore = useChecklistStore();

        const checklistTaxaVoucherArr = Vue.computed(() => checklistStore.getChecklistTaxaVoucherArr);

        function deleteChecklistVoucherRecord(occid) {
            checklistStore.deleteChecklistVoucherRecord(occid, (res) => {
                if(res === 1){
                    checklistStore.setCurrentChecklistTaxonVoucherArr();
                }
                else{
                    showNotification('negative', 'There was an error removing the voucher');
                }
            });
        }

        function openOccurrenceLinkagePopup() {
            context.emit('open:occurrence-linkage-popup');
        }

        return {
            checklistTaxaVoucherArr,
            deleteChecklistVoucherRecord,
            openOccurrenceLinkagePopup
        }
    }
};
