const occurrenceEditorImagePreviewCarousel = {
    props: {
        imageArr: {
            type: Array,
            default: []
        }
    },
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
    setup(props) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const currentImage = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);

        Vue.watch(propsRefs.imageArr, () => {
            setCurrentImage();
        });

        function setCurrentImage() {
            if(props.imageArr.length > 0){
                currentImage.value = props.imageArr[0]['imgid'];
            }
        }

        Vue.onMounted(() => {
            setCurrentImage();
        });

        return {
            clientRoot,
            currentImage
        }
    }
};
