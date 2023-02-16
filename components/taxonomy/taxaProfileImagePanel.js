const taxaProfileImagePanel = {
    props: [
        'taxon'
    ],
    template: `
        <template v-if="taxon.imageCnt > 1">
            <div class="expansion-container">
                <template v-if="taxon.imageCnt < 5">
                    <div class="row">
                        <q-intersection v-for="image in taxon.images" :key="image" class="imgthumb">
                            <q-card class="q-ma-md overflow-hidden">
                                <a :href="image.anchorUrl">
                                    <q-img :src="image.url" :fit="contain" :title="image.caption" :alt="image.sciname"></q-img>
                                    <div class="text-italic photographer">{{ image.sciname }}</div>
                                </a>
                            </q-card>
                        </q-intersection>
                    </div>
                </template>
                <template v-else>
                    <q-expansion-item class="shadow-1 overflow-hidden expansion-element" :label="imageExpansionLabel" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
                        <div class="row">
                            <q-intersection v-for="image in taxon.images" :key="image" class="imgthumb">
                                <q-card class="q-ma-md overflow-hidden">
                                    <a :href="image.anchorUrl">
                                        <q-img :src="image.url" :fit="contain" :title="image.caption" :alt="image.sciname"></q-img>
                                        <div class="text-italic photographer">{{ image.sciname }}</div>
                                    </a>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-expansion-item>
                </template>
            </div>
        </template>
    `
};
