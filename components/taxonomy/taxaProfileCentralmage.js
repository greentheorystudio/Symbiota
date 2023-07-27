const taxaProfileCentralImage = {
    props: [
        'taxon',
        'central-image',
        'is-editor'
    ],
    template: `
        <q-card class="overflow-hidden">
            <template v-if="centralImage">
                <div id="central-image">
                    <a :href="centralImage.anchorUrl">
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
    data() {
        return {
            clientRoot: Vue.ref(CLIENT_ROOT)
        }
    }
};
