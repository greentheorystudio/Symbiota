const mediaInfoWindowPopup = {
    props: {
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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentContainerRef" class="fit">
                    <template v-if="displayImageData">
                        <image-record-info-block :image-data="displayImageData"></image-record-info-block>
                    </template>
                    <template v-else-if="displayMediaData">
                        <media-record-info-block :media-data="displayMediaData"></media-record-info-block>
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
        const contentContainerRef = Vue.ref(null);
        const displayImageData = Vue.ref(null);
        const displayMediaData = Vue.ref(null);

        function closePopup() {
            context.emit('close:popup');
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
            contentContainerRef,
            displayImageData,
            displayMediaData,
            closePopup
        }
    }
};
