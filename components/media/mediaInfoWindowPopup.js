const mediaInfoWindowPopup = {
    props: {
        editor: {
            type: Boolean,
            default: false
        },
        imageData: {
            type: Object,
            default: null
        },
        imageId: {
            type: Number,
            default: null
        },
        mediaData: {
            type: Object,
            default: null
        },
        mediaId: {
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
                <div class="q-mb-xl fit q-pa-md overflow-auto">
                    <template v-if="displayImageData">
                        <div class="q-mb-lg">
                            <image-record-info-block :editor="editor" :image-data="displayImageData" @open:image-editor="openImageEditorPopup"></image-record-info-block>
                        </div>
                    </template>
                    <template v-else-if="displayMediaData">
                        <div class="q-mb-lg">
                            <media-record-info-block :editor="editor" :media-data="displayMediaData" @open:media-editor="openMediaEditorPopup"></media-record-info-block>
                        </div>
                    </template>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'image-record-info-block': imageRecordInfoBlock,
        'media-record-info-block': mediaRecordInfoBlock
    },
    setup(props, context) {
        const displayImageData = Vue.ref(null);
        const displayMediaData = Vue.ref(null);

        function closePopup() {
            context.emit('close:popup');
        }

        function openImageEditorPopup(id) {
            context.emit('open:image-editor', id);
        }

        function openMediaEditorPopup(id) {
            context.emit('open:media-editor', id);
        }

        function setImageData() {
            const formData = new FormData();
            formData.append('imgid', props.imageId.toString());
            formData.append('action', 'getImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                displayImageData.value = Object.assign({}, data);
            });
        }

        function setMediaData() {
            const formData = new FormData();
            formData.append('mediaid', this.mediaId.toString());
            formData.append('action', 'getMediaData');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                displayMediaData.value = Object.assign({}, data);
            });
        }

        Vue.onMounted(() => {
            if(props.imageData){
                displayImageData.value = Object.assign({}, props.imageData);
            }
            else if(props.mediaData){
                displayMediaData.value = Object.assign({}, props.mediaData);
            }
            else if(props.imageId){
                setImageData();
            }
            else if(props.mediaId){
                setMediaData();
            }
        });

        return {
            displayImageData,
            displayMediaData,
            closePopup,
            openImageEditorPopup,
            openMediaEditorPopup
        }
    }
};
