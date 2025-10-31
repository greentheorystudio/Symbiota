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
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(geneticLinkageData['idoccurgenetic']);" icon="fas fa-edit" dense aria-label="Edit genetic record linkage" tabindex="0">
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
                    <div v-if="geneticLinkageData['resourceurl']">
                        <span class="text-bold q-mr-sm">URL:</span> <a :href="geneticLinkageData['resourceurl']" target="_blank" aria-label="External link: View resource - Opens in separate tab" tabindex="0">{{ geneticLinkageData['resourceurl'] }}</a>
                    </div>
                    <div v-if="geneticLinkageData['notes']">
                        <span class="text-bold q-mr-sm">Notes:</span>{{ geneticLinkageData['notes'] }}
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup(_, context) {
        function openEditorPopup(id) {
            context.emit('open:genetic-link-editor', id);
        }

        return {
            openEditorPopup
        }
    }
};
