const taxaProfileImagePanel = {
    template: `
        <template v-if="taxaImageArr.length > 0">
            <div class="expansion-container">
                <template v-if="taxaImageArr.length < 5">
                    <q-card>
                        <div class="q-pt-sm q-pl-md text-h6 text-weight-bold taxon-profile-image-panel-label">
                            Images
                        </div>
                        <div class="row">
                            <q-intersection v-for="image in taxaImageArr" :key="image" class="img-thumb q-mb-sm">
                                <q-card class="q-ma-md overflow-hidden">
                                    <a @click="toggleImageCarousel(image.url);" class="cursor-pointer">
                                        <q-img :src="image.url" class="img-thumb-image" :fit="contain" :title="image.caption" :alt="image.sciname"></q-img>
                                    </a>
                                    <div class="photographer">
                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + image.tid)">
                                            <span class="text-italic">{{ image.sciname }}</span>
                                        </a>
                                    </div>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-card>
                </template>
                <template v-else>
                    <q-expansion-item class="shadow-1 overflow-hidden expansion-element" :label="imageExpansionLabel" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
                        <div class="row">
                            <q-intersection v-for="image in taxaImageArr" :key="image" class="img-thumb q-mb-sm">
                                <q-card class="q-ma-md overflow-hidden">
                                    <a @click="toggleImageCarousel(image.url);" class="cursor-pointer">
                                        <q-img :src="image.url" class="img-thumb-image" :fit="contain" :title="image.caption" :alt="image.sciname"></q-img>
                                    </a>
                                    <div class="photographer">
                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + image.tid)">
                                            <span class="text-italic">{{ image.sciname }}</span>
                                        </a>
                                    </div>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-expansion-item>
                </template>
            </div>
        </template>
    `,
    setup(props, context) {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const imageExpansionLabel = Vue.computed(() => {
            if(Number(taxaImageCount.value) > 100){
                return 'View First 100 Images';
            }
            else{
                return ('View All ' + taxaImageCount.value + ' Images');
            }
        });
        const taxaImageArr = Vue.computed(() => taxaStore.getTaxaImageArr);
        const taxaImageCount = Vue.computed(() => taxaStore.getTaxaImageCount);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function toggleImageCarousel(index) {
            context.emit('update:set-image-carousel', index);
        }

        return {
            clientRoot,
            imageExpansionLabel,
            taxaImageArr,
            taxon,
            toggleImageCarousel
        }
    }
};
