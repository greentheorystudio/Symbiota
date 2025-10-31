const taxaProfileCentralImage = {
    props: {
        image: {
            type: Object,
            default: null
        },
        isEditor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card class="overflow-hidden full-width">
            <template v-if="centralImage">
                <div class="taxon-profile-central-image">
                    <a role="button" @click="toggleImageCarousel(centralImage.url);" class="cursor-pointer" aria-label="Open image carousel" tabindex="0">
                        <q-img :src="(centralImage.url.startsWith('/') ? (clientRoot + centralImage.url) : centralImage.url)" :fit="contain" :title="centralImage.caption" :alt="(centralImage.alttext ? centralImage.alttext : centralImage.sciname)"></q-img>
                        <template v-if="centralImage.photographer || centralImage.caption">
                            <div class="photographer">
                                <template v-if="taxon.sciname !== centralImage.sciname">
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + centralImage.tid)" aria-label="Go to taxon" tabindex="0"><span class="text-italic">{{ centralImage.sciname }}</span>. </a>
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
                        <div><a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + taxon.tid)" aria-label="Add an Image" tabindex="0"><span class="text-weight-bold">Add an Image</span></a></div>
                    </template>
                    <template v-else>
                        <div>Image not available</div>
                    </template>
                </div>
            </template>
        </q-card>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const centralImage = Vue.computed(() => {
            let centralImage;
            if(props.image){
                centralImage = props.image;
            }
            else{
                centralImage = (taxaImageArr.value && taxaImageArr.value.length > 0) ? taxaImageArr.value[0] : null;
            }
            return centralImage;
        });
        const clientRoot = baseStore.getClientRoot;
        const taxaImageArr = Vue.computed(() => taxaStore.getTaxaImageArr);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function toggleImageCarousel(index) {
            context.emit('update:set-image-carousel', index);
        }

        return {
            centralImage,
            clientRoot,
            taxon,
            toggleImageCarousel
        }
    }
};
