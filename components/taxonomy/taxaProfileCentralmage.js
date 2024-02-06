const taxaProfileCentralImage = {
    props: {
        centralImage: {
            type: Object,
            default: {}
        },
        isEditor: {
            type: Boolean,
            default: false
        },
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <q-card class="overflow-hidden">
            <template v-if="centralImage">
                <div id="central-image">
                    <a @click="toggleImageCarousel(centralImage.url);" class="cursor-pointer">
                        <q-img :src="centralImage.url" :fit="contain" :title="centralImage.caption" :alt="centralImage.sciname"></q-img>
                        <template v-if="centralImage.photographer || centralImage.caption">
                            <div class="photographer">
                                <template v-if="taxon.sciName !== centralImage.sciname">
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + centralImage.tid)"><span class="text-italic">{{ centralImage.sciname }}</span>. </a>
                                </template>
                                <span v-if="centralImage.photographer">Photo by: {{ centralImage.photographer }}. </span><span v-html="centralImage.caption"></span>
                            </div>
                        </template>
                    </a>
                </div>
            </template>
            <template v-else>
                <div class="no-central-image">
                    <template v-if="isEditor">
                        <div><a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + taxon.tid)"><span class="text-weight-bold">Add an Image</span></a></div>
                    </template>
                    <template v-else>
                        <div>Image not available</div>
                    </template>
                </div>
            </template>
        </q-card>
    `,
    setup(props, context) {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;

        function toggleImageCarousel(index) {
            context.emit('update:set-image-carousel', index);
        }

        return {
            clientRoot,
            toggleImageCarousel
        }
    }
};
