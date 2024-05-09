const occurrenceEditorImageTranscriberPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="changeImageTranscriberPopupDisplay(false);"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    
                </div>
            </q-card>
        </q-dialog>
    `,
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const changeImageTranscriberPopupDisplay = Vue.inject('changeImageTranscriberPopupDisplay');
        
        return {
            changeImageTranscriberPopupDisplay
        }
    }
};
