const taxaProfileCentralImage = {
    props: [
        'central-image',
        'is-editor',
        'edit-link'
    ],
    template: `
        <q-card>
            <template v-if="centralImage">
                <div id="centralimage">
                    <a :href="centralImage.anchorUrl">
                        <q-img :src="centralImage.url" :fit="contain" :title="centralImage.caption" :alt="centralImage.sciname"></q-img>
                        <template v-if="centralImage.photographer">
                            <div class="photographer">
                                {{ centralImage.photographer }}
                            </div>
                        </template>
                    </a>
                </div>
            </template>
            <template v-else>
                <div id="nocentralimage">
                    <template v-if="isEditor">
                        <div><a :href="editLink"><span class="text-weight-bold">Add an Image</span></a></div>
                    </template>
                    <template v-else>
                        <div>Image not available</div>
                    </template>
                </div>
            </template>
        </q-card>
    `
};
