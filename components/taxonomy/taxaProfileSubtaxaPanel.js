const taxaProfileSubtaxaPanel = {
    props: {
        collapsible: {
            type: Boolean,
            default: true
        },
        expanded: {
            type: Boolean,
            default: false
        },
        isEditor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <template v-if="subtaxaArr.length">
            <div class="q-mb-md expansion-container">
                <template v-if="!collapsible || subtaxaArr.length < 5">
                    <q-card>
                        <div class="q-pt-sm q-pl-md text-h6 text-weight-bold taxon-profile-subtaxa-panel-label">
                            Subtaxa
                        </div>
                        <div class="row">
                            <q-intersection v-for="spptaxon in subtaxaArr" :key="spptaxon">
                                <q-card class="q-ma-md spp-taxon">
                                    <div class="spp-taxon-label">
                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                            {{ spptaxon.sciname }}
                                        </a>
                                    </div>
                                    <div>
                                        <template v-if="subtaxaImageData.hasOwnProperty(spptaxon.tid)">
                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + spptaxon.tid)">
                                                <q-img class="spp-image-container" :src="getSubtaxaImageUrlFromData(subtaxaImageData[spptaxon.tid][0])" fit="scale-down" :title="subtaxaImageData[spptaxon.tid][0]['caption']" :alt="(subtaxaImageData[spptaxon.tid][0]['alttext'] ? subtaxaImageData[spptaxon.tid][0]['alttext'] : spptaxon.sciname)"></q-img>
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
                                                <q-img class="spp-map-container" :src="(taxaMapData[spptaxon.tid]['url'].startsWith('/') ? (clientRoot + taxaMapData[spptaxon.tid]['url']) : taxaMapData[spptaxon.tid]['url'])" fit="scale-down" :title="spptaxon.sciname" :alt="('Map displaying the range of ' + spptaxon.sciname)"></q-img>
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
                    <q-expansion-item v-model="panelExpanded" class="shadow-1 overflow-hidden expansion-element" label="View All Subtaxa" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
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
                                                <q-img :src="getSubtaxaImageUrlFromData(subtaxaImageData[spptaxon.tid][0])" :fit="contain" :title="subtaxaImageData[spptaxon.tid][0]['caption']" :alt="(subtaxaImageData[spptaxon.tid][0]['alttext'] ? subtaxaImageData[spptaxon.tid][0]['alttext'] : spptaxon.sciname)"></q-img>
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
                                                <q-img :src="(taxaMapData[spptaxon.tid]['url'].startsWith('/') ? (clientRoot + taxaMapData[spptaxon.tid]['url']) : taxaMapData[spptaxon.tid]['url'])" :fit="contain" :title="spptaxon.sciname" :alt="('Map displaying the range of ' + spptaxon.sciname)"></q-img>
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
    setup(props) {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const panelExpanded = Vue.ref(false);
        const subtaxaArr = Vue.computed(() => taxaStore.getTaxaChildren);
        const subtaxaImageData = Vue.computed(() => taxaStore.getSubtaxaImageData);
        const taxaMapData = Vue.computed(() => taxaStore.getTaxaMapArr);

        function getSubtaxaImageUrlFromData(imageData) {
            console.log(subtaxaImageData.value);
            if(imageData['thumbnailurl']){
                return (imageData['thumbnailurl'].startsWith('/') ? (clientRoot + imageData['thumbnailurl']) : imageData['thumbnailurl']);
            }
            else{
                return (imageData['url'].startsWith('/') ? (clientRoot + imageData['url']) : imageData['url']);
            }
        }

        Vue.onMounted(() => {
            console.log(subtaxaImageData.value);
            panelExpanded.value = props.expanded;
        });

        return {
            clientRoot,
            panelExpanded,
            subtaxaArr,
            subtaxaImageData,
            taxaMapData,
            getSubtaxaImageUrlFromData
        }
    }
};
