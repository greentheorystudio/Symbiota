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
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(checklistTaxaId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="edit" label="Edit" no-caps></q-tab>
                                <q-tab name="images" label="Images" no-caps></q-tab>
                                <q-tab v-if="checklistData['searchterms']" name="vouchers" label="Vouchers" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="edit">
                                    <checklist-taxa-add-edit-module @close:popup="closePopup();"></checklist-taxa-add-edit-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="images">
                                    <checklist-taxa-image-selector-module></checklist-taxa-image-selector-module>
                                </q-tab-panel>
                                <q-tab-panel v-if="checklistData['searchterms']" class="q-pa-none" name="vouchers">
                                    
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
    `,
    components: {
        'checklist-taxa-add-edit-module': checklistTaxaAddEditModule,
        'checklist-taxa-image-selector-module': checklistTaxaImageSelectorModule
    },
    setup(props, context) {
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
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
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 100) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            checklistStore.setCurrentChecklistTaxonRecord(props.checklistTaxaId);
        });

        return {
            checklistData,
            contentRef,
            contentStyle,
            tab,
            tabStyle,
            closePopup
        }
    }
};
