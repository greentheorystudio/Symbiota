const occurrenceEditorGeneticLinkModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section>
                <div class="column q-gutter-sm">
                    <div class="row justify-between q-gutter-sm">
                        <div class="text-h6 text-bold">Genetic Record Linkages</div>
                        <div>
                            <q-btn color="secondary" @click="openGeneticLinkageEditorPopup(0);" label="Add New Genetic Record Linkage" tabindex="0" />
                        </div>
                    </div>
                    <div class="q-mt-sm column q-gutter-sm">
                        <template v-if="geneticLinkageArr.length > 0">
                            <template v-for="linkage in geneticLinkageArr">
                                <genetic-link-record-info-block :genetic-linkage-data="linkage" :editor="true" @open:genetic-link-editor="openGeneticLinkageEditorPopup"></genetic-link-record-info-block>
                            </template>
                        </template>
                        <template v-else>
                            <span class="text-body1 text-bold">There are no genetic records associated with this record.</span>
                        </template>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showGeneticLinkageEditorPopup">
            <occurrence-genetic-record-linkage-editor-popup
                :genetic-linkage-id="editGeneticLinkageId"
                :show-popup="showGeneticLinkageEditorPopup"
                @close:popup="showGeneticLinkageEditorPopup = false"
            ></occurrence-genetic-record-linkage-editor-popup>
        </template>
    `,
    components: {
        'genetic-link-record-info-block': geneticLinkRecordInfoBlock,
        'occurrence-genetic-record-linkage-editor-popup': occurrenceGeneticRecordLinkageEditorPopup
    },
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const geneticLinkageArr = Vue.computed(() => occurrenceStore.getGeneticLinkArr);
        const editGeneticLinkageId = Vue.ref(0);
        const showGeneticLinkageEditorPopup = Vue.ref(false);

        function openGeneticLinkageEditorPopup(id) {
            editGeneticLinkageId.value = id;
            showGeneticLinkageEditorPopup.value = true;
        }

        return {
            geneticLinkageArr,
            editGeneticLinkageId,
            showGeneticLinkageEditorPopup,
            openGeneticLinkageEditorPopup
        }
    }
};
