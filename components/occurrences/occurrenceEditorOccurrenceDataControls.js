const occurrenceEditorOccurrenceDataControls = {
    props: {
        editingActivated: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="row justify-between">
            <div>
                <template v-if="Number(occId) === 0">
                    <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                </template>
            </div>
            <div class="row justify-end">
                <template v-if="Number(occId) === 0">
                    <q-btn color="secondary" @click="createOccurrenceRecord();" label="Create Occurrence Record" />
                </template>
                <template v-else>
                    <template v-if="!editingActivated">
                        <q-btn color="green" @click="setEditingActivated(true);" label="Edit Occurrence Data" />
                    </template>
                    <template v-else>
                        <q-btn color="red" @click="setEditingActivated(false);" label="Stop Editing" />
                    </template>
                </template>
            </div>
        </div>
    `,
    components: {
        'occurrence-entry-follow-up-action-selector': occurrenceEntryFollowUpActionSelector
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const additionalDataFields = Vue.computed(() => occurrenceStore.getAdditionalDataFields);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);

        const setEditingActivated = Vue.inject('setEditingActivated');

        function changeEntryFollowUpAction(value) {
            occurrenceStore.setEntryFollowUpAction(value);
        }

        Vue.onMounted(() => {
            if(occId.value){
                if(Number(occId.value) > 0){
                    if(entryFollowUpAction.value === 'remain'){
                        setEditingActivated(true);
                    }
                    occurrenceStore.setEntryFollowUpAction('none');
                }
                else{
                    setEditingActivated(true);
                    occurrenceStore.setEntryFollowUpAction('remain');
                }
            }
        });

        return {
            additionalDataFields,
            entryFollowUpAction,
            occId,
            changeEntryFollowUpAction,
            setEditingActivated
        }
    }
};
