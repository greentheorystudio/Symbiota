const spatialViewerPopup = {
    props: {
        coordinateSet: {
            type: Array,
            default: []
        },
        footprintWkt: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div class="fit">
                    <spatial-viewer-element :coordinate-set="coordinateSet" :footprint-wkt="footprintWkt"></spatial-viewer-element>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'spatial-viewer-element': spatialViewerElement
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
