const taxaProfileSubtaxaPanel = {
    props: [
        'subtaxa-arr',
        'subtaxa-label',
        'subtaxa-expansion-label',
        'is-editor'
    ],
    template: `
        <template v-if="subtaxaArr.length">
            <div class="expansion-container">
                <template v-if="subtaxaArr.length < 5">
                    <q-card>
                        <div class="q-pt-sm q-pl-md text-h6 text-weight-bold">
                            {{ subtaxaLabel }}
                        </div>
                        <div class="row">
                            <q-intersection v-for="spptaxon in subtaxaArr" :key="spptaxon" class="spp-taxon">
                                <q-card class="q-ma-md overflow-hidden">
                                    <div class="spp-taxon-label">
                                        <a :href="spptaxon.taxaurl">
                                            {{ spptaxon.sciName }}
                                        </a>
                                    </div>
                                    <div class="spp-image-container">
                                        <template v-if="spptaxon.url">
                                            <a :href="spptaxon.taxaurl">
                                                <q-img :src="spptaxon.url" :fit="contain" :title="spptaxon.caption" :alt="spptaxon.sciName"></q-img>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <div class="no-spptaxon-image">
                                                <template v-if="isEditor">
                                                    <a :href="spptaxon.editurl">Add an Image</a>
                                                </template>
                                                <template v-else>
                                                    Image not available
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <template v-if="spptaxon.rankid > 140">
                                        <div class="spp-map-container">
                                            <template v-if="spptaxon.map">
                                                <q-img :src="spptaxon.map" :fit="contain" :title="spptaxon.sciName" :alt="spptaxon.sciName"></q-img>
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
                    <q-expansion-item class="shadow-1 overflow-hidden expansion-element" :label="subtaxaExpansionLabel" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
                        <div class="row">
                            <q-intersection v-for="spptaxon in subtaxaArr" :key="spptaxon" class="spp-taxon">
                                <q-card class="q-ma-md overflow-hidden">
                                    <div class="spp-taxon-label">
                                        <a :href="spptaxon.taxaurl">
                                            {{ spptaxon.sciName }}
                                        </a>
                                    </div>
                                    <div class="spp-image-container">
                                        <template v-if="spptaxon.url">
                                            <a :href="spptaxon.taxaurl">
                                                <q-img :src="spptaxon.url" :fit="contain" :title="spptaxon.caption" :alt="spptaxon.sciName"></q-img>
                                            </a>
                                        </template>
                                        <template v-else>
                                            <div class="no-spptaxon-image">
                                                <template v-if="isEditor">
                                                    <a :href="spptaxon.editurl">Add an Image</a>
                                                </template>
                                                <template v-else>
                                                    Image not available
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <template v-if="spptaxon.rankid > 140">
                                        <div class="spp-map-container">
                                            <template v-if="spptaxon.map">
                                                <q-img :src="spptaxon.map" :fit="contain" :title="spptaxon.sciName" :alt="spptaxon.sciName"></q-img>
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
    `
};
