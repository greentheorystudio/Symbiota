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
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <spatial-viewer-element :coordinate-set="coordinateSet" :footprint-wkt="footprintWkt"></spatial-viewer-element>
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
