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
        <div>
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
                                        <a :href="image.anchorUrl" target="_blank">See image details</a>
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
    `,
    data() {
        return {
            clientRoot: Vue.ref(CLIENT_ROOT)
        }
    },
    methods: {
        hideImageCarousel(){
            this.$emit('update:show-image-carousel', false);
        },
        updateCurrentImage(val){
            this.$emit('update:current-image', val);
        }
    }
};
