const occurrenceEditorBatchUpdatePopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="changeBatchUpdatePopupDisplay(false);"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    
                </div>
            </q-card>
        </q-dialog>
    `,
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const changeBatchUpdatePopupDisplay = Vue.inject('changeBatchUpdatePopupDisplay');
        
        return {
            changeBatchUpdatePopupDisplay
        }
    }
};
