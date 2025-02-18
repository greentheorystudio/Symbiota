const taxaProfileImageCarousel = {
    props: {
        imageArr: {
            type: Array,
            default: []
        },
        imageIndex: {
            type: String,
            default: ''
        }
    },
    template: `
        <div v-if="!showOccurrenceInfoPopup && !showMediaInfoPopup">
            <q-carousel swipeable animated v-model="imageIndex" thumbnails arrows infinite class="taxon-profile-image-carousel" height="94vh" @update:model-value="updateCurrentImage">
                <template v-for="image in imageArr" :key="image">
                    <q-carousel-slide :name="image.url" :img-src="image.url" class="image-carousel-image">
                        <div class="absolute-top-left q-pa-md">
                            <div class="text-black rounded-borders taxon-profile-image-carousel-info-box">
                                <template v-if="image.sciname"><span class="text-italic">{{ image.sciname }}</span>. </template>
                                <template v-if="image.photographer">Photo by: {{ image.photographer }}. </template>
                                <template v-if="image.caption">{{ image.caption }} </template>
                                <template v-if="image.owner">Image provided by: {{ image.owner }}. </template>
                                <div class="row justify-between q-gutter-md">
                                    <div>
                                        <a class="cursor-pointer" @click="openPopup(image);">See image details</a>
                                    </div>
                                    <div>
                                        <a :href="image.url" target="_blank">View full size</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-carousel-slide>
                </template>
                <template v-slot:control>
                    <q-carousel-control position="top-right" :offset="[0, 0]">
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="hideImageCarousel();"></q-btn>
                    </q-carousel-control>
                </template>
            </q-carousel>
        </div>
        <template v-if="showOccurrenceInfoPopup">
            <occurrence-info-window-popup :occurrence-id="occurrenceId" :show-popup="showOccurrenceInfoPopup" @close:popup="closePopup"></occurrence-info-window-popup>
        </template>
        <template v-if="showMediaInfoPopup">
            <media-info-window-popup :image-data="imageData" :show-popup="showMediaInfoPopup" @close:popup="closePopup"></media-info-window-popup>
        </template>
    `,
    components: {
        'media-info-window-popup': mediaInfoWindowPopup,
        'occurrence-info-window-popup': occurrenceInfoWindowPopup
    },
    setup(props, context) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const imageData = Vue.ref(null);
        const occurrenceId = Vue.ref(null);
        const showMediaInfoPopup = Vue.ref(false);
        const showOccurrenceInfoPopup = Vue.ref(false);

        function closePopup() {
            showOccurrenceInfoPopup.value = false;
            showMediaInfoPopup.value = false;
            occurrenceId.value = null;
            imageData.value = null;
        }

        function hideImageCarousel() {
            context.emit('update:show-image-carousel', false);
        }

        function openPopup(image) {
            if(Number(image['occid']) > 0){
                occurrenceId.value = image['occid'];
                showOccurrenceInfoPopup.value = true;
            }
            else{
                imageData.value = Object.assign({}, image);
                showMediaInfoPopup.value = true;
            }
        }

        function updateCurrentImage(val) {
            context.emit('update:current-image', val);
        }

        return {
            clientRoot,
            imageData,
            occurrenceId,
            showMediaInfoPopup,
            showOccurrenceInfoPopup,
            closePopup,
            hideImageCarousel,
            openPopup,
            updateCurrentImage
        }
    }
};
