const taxaProfileCentralImage = {
    props: {
        isEditor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card class="overflow-hidden fit">
            <template v-if="centralImage">
                <div class="taxon-profile-central-image">
                    <a @click="toggleImageCarousel(centralImage.url);" class="cursor-pointer">
                        <q-img :src="centralImage.url" :fit="contain" :title="centralImage.caption" :alt="centralImage.sciname"></q-img>
                        <template v-if="centralImage.photographer || centralImage.caption">
                            <div class="photographer">
                                <template v-if="taxon.sciname !== centralImage.sciname">
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
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const centralImage = Vue.computed(() => {
            return (taxaImageArr.value && taxaImageArr.value.length > 0) ? taxaImageArr.value[0] : null;
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
