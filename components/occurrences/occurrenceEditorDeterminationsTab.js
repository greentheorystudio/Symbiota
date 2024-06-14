const occurrenceEditorDeterminationsTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="row justify-end">
                <q-btn color="secondary" @click="openDeterminationEditorPopup(0);" label="Add New Determination" />
            </div>
            <div class="q-mt-sm">
                <template v-if="determinationArr.length > 0">
                    <div class="q-my-sm text-h6 text-bold">Determination History</div>
                    <template v-for="determination in determinationArr">
                        <q-card flat bordered>
                            <q-card-section>
                                <div class="column">
                                    <div class="row justify-between">
                                        <div class="row justify-start q-gutter-sm">
                                            <div v-if="determination['identificationqualifier']">
                                                {{ determination['identificationqualifier'] + ' ' }}
                                            </div>
                                            <div class="text-bold text-italic">
                                                {{ determination['sciname'] + ' ' }}
                                            </div>
                                            <div v-if="determination['scientificnameauthorship']">
                                                {{ determination['scientificnameauthorship'] + ' ' }}
                                            </div>
                                            <div v-if="Number(determination['iscurrent']) === 1" class="text-red q-pl-md">
                                                CURRENT DETERMINATION
                                            </div>
                                        </div>
                                        <div class="row justify-end">
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openDeterminationEditorPopup(determination['detid']);" icon="fas fa-edit" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Edit determination record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div v-if="determination['verbatimscientificname']">
                                        <span class="text-bold q-mr-sm">Verbatim Scientific Name:</span>{{ determination['verbatimscientificname'] }}
                                    </div>
                                    <div class="row justify-start q-gutter-md">
                                        <div>
                                            <span class="text-bold q-mr-sm">Determiner:</span>{{ determination['identifiedby'] }}
                                        </div>
                                        <div>
                                            <span class="text-bold q-mr-sm">Date:</span>{{ determination['dateidentified'] }}
                                        </div>
                                    </div>
                                    <div v-if="determination['identificationreferences']">
                                        <span class="text-bold q-mr-sm">Reference:</span>{{ determination['identificationreferences'] }}
                                    </div>
                                    <div v-if="determination['identificationremarks']">
                                        <span class="text-bold q-mr-sm">Notes:</span>{{ determination['identificationremarks'] }}
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                </template>
                <template v-else>
                    <span class="text-h6 text-bold">There are no previous determinations for this record.</span>
                </template>
            </div>
        </div>
        <template v-if="showDeterminationEditorPopup">
            <occurrence-determination-editor-popup
                    :determination-id="editDeterminationId"
                    :show-popup="showDeterminationEditorPopup"
                    @close:popup="showDeterminationEditorPopup = false"
            ></occurrence-determination-editor-popup>
        </template>
    `,
    components: {
        'occurrence-determination-editor-popup': occurrenceDeterminationEditorPopup
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const determinationArr = Vue.computed(() => occurrenceStore.getDeterminationArr);
        const editDeterminationId = Vue.ref(0);
        const showDeterminationEditorPopup = Vue.ref(false);

        function openDeterminationEditorPopup(id) {
            editDeterminationId.value = id;
            showDeterminationEditorPopup.value = true;
        }

        return {
            determinationArr,
            editDeterminationId,
            showDeterminationEditorPopup,
            openDeterminationEditorPopup
        }
    }
};
