const occurrenceInfoWindowPopup = {
    props: {
        occurrenceId: {
            type: Number,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <occurrence-info-tab-module :occurrence-id="occurrenceId"></occurrence-info-tab-module>
            </q-card>
        </q-dialog>
    `,
    components: {
        'occurrence-info-tab-module': occurrenceInfoTabModule
    },
    setup(props, context) {
        function closePopup() {
            context.emit('close:popup');
        }

        return {
            closePopup
        }
    }
};
