const imageCarousel = {
    props: {
        imageArr: {
            type: Array,
            default: []
        }
    },
    template: `
        <q-card flat bordered class="fit preview-image-carousel">
            <q-card-section class="fit">
                <q-carousel swipeable animated v-model="currentImage" :arrows="(imageArr.length > 1)" control-color="black" infinite class="fit">
                    <template v-for="image in imageArr" :key="image">
                        <q-carousel-slide v-if="image.url" :name="image.imgid" class="column no-wrap">
                            <div class="row fit justify-start items-center q-gutter-xs q-col-gutter no-wrap">
                                <q-img class="rounded-borders fit" :src="(image.url.startsWith('/') ? (clientRoot + image.url) : image.url)" fit="scale-down"></q-img>
                            </div>
                        </q-carousel-slide>
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
