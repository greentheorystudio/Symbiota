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
                        <img :src="centralImage.url" class="central-image" :title="centralImage.caption" :alt="centralImage.sciname" />
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
                        <a :href="editLink"><span class="text-weight-bold">Add an Image</span></a>
                    </template>
                    <template v-else>
                        Image<br />not available
                    </template>
                </div>
            </template>
        </q-card>
    `
};
