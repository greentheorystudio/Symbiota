const occurrenceEditorImagePreviewCarousel = {
    template: `
        <q-card flat bordered class="fit occurrence-editor-preview-image-carousel">
            <q-card-section class="fit">
                <q-carousel ref="carousel" swipeable animated v-model="currentImage" :arrows="(imageArr.length > 1)" control-color="black" infinite class="fit">
                    <template v-for="image in imageArr" :key="image">
                        <q-carousel-slide :name="image.imgid" :img-src="(image.url.startsWith('/') ? (clientRoot + image.url) : image.url)" class="fit"></q-carousel-slide>
                    </template>
                </q-carousel>
            </q-card-section>
        </q-card>
    `,
    setup() {
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();

        const clientRoot = baseStore.getClientRoot;
        const currentImage = Vue.ref(null);
        const imageArr = Vue.computed(() => occurrenceStore.getImageArr);

        Vue.watch(imageArr, () => {
            setCurrentImage();
        });

        function setCurrentImage() {
            if(imageArr.value.length > 0){
                currentImage.value = imageArr.value[0]['imgid'];
            }
        }

        Vue.onMounted(() => {
            setCurrentImage();
        });

        return {
            clientRoot,
            currentImage,
            imageArr
        }
    }
};
