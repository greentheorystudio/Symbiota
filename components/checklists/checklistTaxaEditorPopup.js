const checklistTaxaEditorPopup = {
    props: {
        checklistTaxaId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showOccurrenceLinkageToolPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(checklistTaxaId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="edit" label="Info" no-caps></q-tab>
                                <q-tab name="images" label="Images" no-caps></q-tab>
                                <q-tab name="vouchers" label="Vouchers" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="edit">
                                    <checklist-taxa-add-edit-module @close:popup="closePopup();"></checklist-taxa-add-edit-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="images">
                                    <checklist-taxa-image-selector-module></checklist-taxa-image-selector-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="vouchers">
                                    <checklist-taxa-voucher-module @open:occurrence-linkage-popup="showOccurrenceLinkageToolPopup = true"></checklist-taxa-voucher-module>
                                </q-tab-panel>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <checklist-taxa-add-edit-module @close:popup="closePopup();"></checklist-taxa-add-edit-module>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showOccurrenceLinkageToolPopup">
            <occurrence-linkage-tool-popup
                :show-popup="showOccurrenceLinkageToolPopup"
                :editor-limit="false"
                :avoid-arr="checklistTaxaVoucherOccidArr"
                :search-terms="linkageToolSearchTerms"
                @update:occid="updateOccurrenceLinkage"
                @close:popup="showOccurrenceLinkageToolPopup = false"
            ></occurrence-linkage-tool-popup>
        </template>
    `,
    components: {
        'checklist-taxa-add-edit-module': checklistTaxaAddEditModule,
        'checklist-taxa-image-selector-module': checklistTaxaImageSelectorModule,
        'checklist-taxa-voucher-module': checklistTaxaVoucherModule,
        'occurrence-linkage-tool-popup': occurrenceLinkageToolPopup
    },
    setup(props, context) {
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);
        const checklistTaxaData = Vue.computed(() => checklistStore.getChecklistTaxaData);
        const checklistTaxaVoucherOccidArr = Vue.computed(() => checklistStore.getChecklistTaxaVoucherOccidArr);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const linkageToolSearchTerms = Vue.computed(() => {
            let returnObj = {};
            if(checklistData.value['searchterms']){
                returnObj = Object.assign({}, checklistData.value['searchterms']);
            }
            returnObj['usethes'] = true;
            returnObj['taxa'] = Number(checklistTaxaData.value['tid']);
            returnObj['sciname'] = checklistTaxaData.value['sciname'];
            return returnObj;
        });
        const showOccurrenceLinkageToolPopup = Vue.ref(false);
        const tab = Vue.ref('edit');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            checklistStore.setCurrentChecklistTaxonRecord(props.checklistTaxaId);
        });

        return {
            checklistTaxaVoucherOccidArr,
            contentRef,
            contentStyle,
            linkageToolSearchTerms,
            showOccurrenceLinkageToolPopup,
            tab,
            tabStyle,
            closePopup
        }
    }
};
