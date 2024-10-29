const determinationRecordInfoBlock = {
    props: {
        determinationData: {
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
                        <div class="row justify-start q-gutter-sm">
                            <div v-if="determinationData['identificationqualifier']">
                                {{ determinationData['identificationqualifier'] + ' ' }}
                            </div>
                            <div class="text-bold text-italic">
                                {{ determinationData['sciname'] + ' ' }}
                            </div>
                            <div v-if="determinationData['scientificnameauthorship']">
                                {{ determinationData['scientificnameauthorship'] + ' ' }}
                            </div>
                            <div v-if="Number(determinationData['iscurrent']) === 1" class="text-red q-pl-md">
                                CURRENT DETERMINATION
                            </div>
                        </div>
                        <div v-if="editor" class="row justify-end">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(determinationData['detid']);" icon="fas fa-edit" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit determination record
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <div v-if="determinationData['verbatimscientificname']">
                        <span class="text-bold q-mr-sm">Verbatim Scientific Name:</span>{{ determinationData['verbatimscientificname'] }}
                    </div>
                    <div class="row justify-start q-gutter-md">
                        <div>
                            <span class="text-bold q-mr-sm">Determiner:</span>{{ determinationData['identifiedby'] }}
                        </div>
                        <div>
                            <span class="text-bold q-mr-sm">Date:</span>{{ determinationData['dateidentified'] }}
                        </div>
                    </div>
                    <div v-if="determinationData['identificationreferences']">
                        <span class="text-bold q-mr-sm">Reference:</span>{{ determinationData['identificationreferences'] }}
                    </div>
                    <div v-if="determinationData['identificationremarks']">
                        <span class="text-bold q-mr-sm">Notes:</span>{{ determinationData['identificationremarks'] }}
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup(_, context) {
        function openEditorPopup(id) {
            context.emit('open:determination-editor', id);
        }

        return {
            openEditorPopup
        }
    }
};
