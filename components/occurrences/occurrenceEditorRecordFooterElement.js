const occurrenceEditorRecordFooterElement = {
    template: `
        <div class="q-mt-sm q-px-sm row justify-between">
            <div>
                Key: {{ occurrenceData.occid }}
            </div>
            <div>
                <template v-if="occurrenceData.datelastmodified">
                    Modified: {{ occurrenceData.datelastmodified }}
                </template>
            </div>
            <div>
                <template v-if="occurrenceData.recordenteredby || occurrenceData.dateentered">
                    <span v-if="occurrenceData.recordenteredby">
                        Entered by: {{ occurrenceData.recordenteredby + ' ' }}
                    </span>
                    <span v-if="occurrenceData.dateentered">
                        {{ '[' + occurrenceData.dateentered + ']' }}
                    </span>
                </template>
            </div>
        </div>
    `,
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);

        return {
            occurrenceData
        }
    }
};
