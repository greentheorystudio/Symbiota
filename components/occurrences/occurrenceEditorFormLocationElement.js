const occurrenceEditorFormLocationElement = {
    props: {
        editingActivated: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-gutter-sm">
                <div class="row justify-between q-gutter-sm">
                    <div class="col">
                        <q-input outlined v-model="occurrenceData.country" label="Country" dense>
                            <template v-slot:append>
                                <q-icon name="cancel" class="cursor-pointer" @click="occurrenceData.country = null"></q-icon>
                                <q-icon name="far fa-question-circle" class="cursor-pointer"></q-icon>
                            </template>
                        </q-input>
                    </div>
                    <div class="col">
                        <q-input outlined v-model="occurrenceData.stateprovince" label="State/Province" dense></q-input>
                    </div>
                    <div class="col">
                        <q-input outlined v-model="occurrenceData.county" label="County" dense></q-input>
                    </div>
                    <div class="col">
                        <q-input outlined v-model="occurrenceData.municipality" label="Municipality" dense></q-input>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);

        return {
            occurrenceData
        }
    }
};
