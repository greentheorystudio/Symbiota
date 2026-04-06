const occurrenceInfoWindowPopup = {
    props: {
        navigatorMode: {
            type: Boolean,
            default: false
        },
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
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <occurrence-info-tab-module :occurrence-id="occurrenceId" :navigator-mode="navigatorMode"></occurrence-info-tab-module>
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
