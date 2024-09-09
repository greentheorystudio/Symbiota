const geneticLinkRecordInfoBlock = {
    props: {
        geneticLinkageData: {
            type: Object,
            default: null
        },
        editor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card>
            <q-card-section>
                <div class="column">
                    <div class="row justify-between">
                        <div>
                            {{ geneticLinkageData['resourcename'] }}
                        </div>
                        <div v-if="editor" class="row justify-end">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(geneticLinkageData['idoccurgenetic']);" icon="fas fa-edit" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit genetic record linkage
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <div v-if="geneticLinkageData['identifier']">
                        <span class="text-bold q-mr-sm">Identifier:</span>{{ geneticLinkageData['identifier'] }}
                    </div>
                    <div v-if="geneticLinkageData['locus']">
                        <span class="text-bold q-mr-sm">Locus:</span>{{ geneticLinkageData['locus'] }}
                    </div>
                    <div v-if="geneticLinkageData['verbatimscientificname']">
                        <span class="text-bold q-mr-sm">URL:</span> <a :href="geneticLinkageData['resourceurl']" target="_blank">{{ geneticLinkageData['resourceurl'] }}</a>
                    </div>
                    <div v-if="geneticLinkageData['notes']">
                        <span class="text-bold q-mr-sm">Notes:</span>{{ geneticLinkageData['notes'] }}
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="editor && showGeneticLinkageEditorPopup">
            <occurrence-genetic-record-linkage-editor-popup
                :genetic-linkage-id="editGeneticLinkageId"
                :show-popup="showGeneticLinkageEditorPopup"
                @close:popup="showGeneticLinkageEditorPopup = false"
            ></occurrence-genetic-record-linkage-editor-popup>
        </template>
    `,
    components: {
        'occurrence-genetic-record-linkage-editor-popup': occurrenceGeneticRecordLinkageEditorPopup
    },
    setup() {
        const editGeneticLinkageId = Vue.ref(0);
        const showGeneticLinkageEditorPopup = Vue.ref(false);

        function openEditorPopup(id) {
            editGeneticLinkageId.value = id;
            showGeneticLinkageEditorPopup.value = true;
        }

        return {
            editGeneticLinkageId,
            showGeneticLinkageEditorPopup,
            openEditorPopup
        }
    }
};
