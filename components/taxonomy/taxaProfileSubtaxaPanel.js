const taxaProfileSubtaxaPanel = {
    props: {
        collapsible: {
            type: Boolean,
            default: true
        },
        isEditor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <template v-if="subtaxaArr.length">
            <div class="expansion-container">
                <template v-if="!collapsible || subtaxaArr.length < 5">
                    <q-card>
                        <div class="q-pt-sm q-pl-md text-h6 text-weight-bold taxon-profile-subtaxa-panel-label">
                            Subtaxa
                        </div>
                        <div class="row">
                            <q-intersection v-for="spptaxon in subtaxaArr" :key="spptaxon" :class="{'spp-taxon':true, 'below-family':(spptaxon.rankid > 140), 'family-or-above':(spptaxon.rankid <= 140)}">
                                <q-card class="q-ma-md overflow-hidden">
                                    <div class="spp-taxon-label">
                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                            {{ spptaxon.sciname }}
                                        </a>
                                    </div>
                                    <div class="spp-image-container">
                                        <template v-if="subtaxaImageData.hasOwnProperty(spptaxon.tid)">
                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                                <q-img :src="subtaxaImageData[spptaxon.tid][0]['thumbnailurl'] ? subtaxaImageData[spptaxon.tid][0]['thumbnailurl'] : subtaxaImageData[spptaxon.tid][0]['url']" :fit="contain" :title="spptaxon.caption" :alt="spptaxon.sciname"></q-img>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <div class="no-spptaxon-image">
                                                <template v-if="isEditor">
                                                    <a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + spptaxon.tid)">Add an Image</a>
                                                </template>
                                                <template v-else>
                                                    Image not available
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <template v-if="spptaxon.rankid > 140">
                                        <div class="spp-map-container">
                                            <template v-if="taxaMapData.hasOwnProperty(spptaxon.tid)">
                                                <q-img :src="taxaMapData[spptaxon.tid]['url']" :fit="contain" :title="spptaxon.sciname" :alt="spptaxon.sciname"></q-img>
                                            </template>
                                            <template v-else>
                                                <div class="no-spptaxon-image">
                                                    Map not available
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-card>
                </template>
                <template v-else>
                    <q-expansion-item class="shadow-1 overflow-hidden expansion-element" label="View All Subtaxa" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
                        <div class="row">
                            <q-intersection v-for="spptaxon in subtaxaArr" :key="spptaxon" :class="{'spp-taxon':true, 'below-family':(spptaxon.rankid > 140), 'family-or-above':(spptaxon.rankid <= 140)}">
                                <q-card class="q-ma-md overflow-hidden">
                                    <div class="spp-taxon-label">
                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                            {{ spptaxon.sciname }}
                                        </a>
                                    </div>
                                    <div class="spp-image-container">
                                        <template v-if="subtaxaImageData.hasOwnProperty(spptaxon.tid)">
                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                                <q-img :src="subtaxaImageData[spptaxon.tid][0]['thumbnailurl'] ? subtaxaImageData[spptaxon.tid][0]['thumbnailurl'] : subtaxaImageData[spptaxon.tid][0]['url']" :fit="contain" :title="spptaxon.caption" :alt="spptaxon.sciname"></q-img>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <div class="no-spptaxon-image">
                                                <template v-if="isEditor">
                                                    <a :href="(clientRoot + '/taxa/profile/tpeditor.php?tid=' + spptaxon.tid)">Add an Image</a>
                                                </template>
                                                <template v-else>
                                                    Image not available
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <template v-if="spptaxon.rankid > 140">
                                        <div class="spp-map-container">
                                            <template v-if="taxaMapData.hasOwnProperty(spptaxon.tid)">
                                                <q-img :src="taxaMapData[spptaxon.tid]['url']" :fit="contain" :title="spptaxon.sciname" :alt="spptaxon.sciname"></q-img>
                                            </template>
                                            <template v-else>
                                                <div class="no-spptaxon-image">
                                                    Map not available
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-expansion-item>
                </template>
            </div>
        </template>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const subtaxaArr = Vue.computed(() => taxaStore.getTaxaChildren);
        const subtaxaImageData = Vue.computed(() => taxaStore.getSubtaxaImageData);
        const taxaMapData = Vue.computed(() => taxaStore.getTaxaMapArr);

        return {
            clientRoot,
            subtaxaArr,
            subtaxaImageData,
            taxaMapData
        }
    }
};
