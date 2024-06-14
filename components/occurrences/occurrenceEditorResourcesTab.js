const occurrenceEditorResourcesTab = {
    template: `
        <div class="column q-gutter-sm">
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
            <q-card flat bordered class="black-border">
                <q-card-section>
                    <div class="row justify-between q-gutter-sm">
                        <div class="text-h6 text-bold">Genetic Record Linkages</div>
                        <div>
                            <q-btn color="secondary" @click="showGeneticLinkageEditorPopup = true" label="Add New Genetic Record Linkage" />
                        </div>
                    </div>
                    <div class="q-mt-xs q-pl-sm column q-gutter-sm">
                        
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <template v-if="showGeneticLinkageEditorPopup">
            <occurrence-genetic-record-linkage-editor-popup
                    :show-popup="showGeneticLinkageEditorPopup"
                    @close:popup="showGeneticLinkageEditorPopup = false"
            ></occurrence-genetic-record-linkage-editor-popup>
        </template>
    `,
    components: {
        'occurrence-genetic-record-linkage-editor-popup': occurrenceGeneticRecordLinkageEditorPopup
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const showGeneticLinkageEditorPopup = Vue.ref(false);

        return {
            showGeneticLinkageEditorPopup
        }
    }
};
