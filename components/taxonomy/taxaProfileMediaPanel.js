const taxaProfileMediaPanel = {
    props: [
        'taxon'
    ],
    template: `
        <template v-if="taxon.media.length > 0">
            <div class="expansion-container">
                <template v-if="taxon.media.length">
                    <q-card>
                        <div class="q-pt-sm q-pl-md text-h6 text-weight-bold">
                            Audio & Video
                        </div>
                        <div class="row">
                            <q-intersection v-for="media in taxon.media" :key="media" :class="{'media-thumb':true, 'video-thumb':(media.format.startsWith('video')), 'audio-thumb':(media.format.startsWith('audio'))}">
                                <q-card class="q-ma-md overflow-hidden">
                                    <template v-if="media.format.startsWith('video')">
                                        <div class="video-player-container">
                                            <video class="video-player" controls>
                                                <source :src="media.accessuri" :type="media.format">
                                            </video>
                                        </div>
                                    </template>
                                    <template v-else-if="media.format.startsWith('audio')">
                                        <div class="audio-player-container">
                                            <audio class="audio-player" controls>
                                                <source :src="media.accessuri" :type="media.format">
                                            </audio>
                                        </div>
                                    </template>
                                    <div class="media-info">
                                        <template v-if="taxon.sciName !== media.sciname">
                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + media.tid)"><span class="text-italic">{{ media.sciname }}</span>. </a>
                                        </template>
                                        <span v-if="media.title">{{ media.title }} - </span>
                                        {{ media.description }}
                                        <span v-if="media.creator">Created by: {{ media.creator }}. </span>
                                        <span v-if="media.owner">Provided by: {{ media.owner }}.</span>
                                    </div>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-card>
                </template>
                <template v-else>
                    <q-expansion-item class="shadow-1 overflow-hidden expansion-element" label="View All Audio & Video" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
                        <div class="row">
                            <q-intersection v-for="media in taxon.media" :key="media" :class="{'media-thumb':true, 'video-thumb':(media.format.startsWith('video')), 'audio-thumb':(media.format.startsWith('audio'))}">
                                <q-card class="q-ma-md overflow-hidden">
                                    <template v-if="media.format.startsWith('video')">
                                        <div class="video-player-container">
                                            <video class="video-player" controls>
                                                <source :src="media.accessuri" :type="media.format">
                                            </video>
                                        </div>
                                    </template>
                                    <template v-else-if="media.format.startsWith('audio')">
                                        <div class="audio-player-container">
                                            <audio class="audio-player" controls>
                                                <source :src="media.accessuri" :type="media.format">
                                            </audio>
                                        </div>
                                    </template>
                                    <div class="media-info">
                                        <template v-if="taxon.sciName !== media.sciname">
                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + media.tid)"><span class="text-italic">{{ media.sciname }}</span>. </a>
                                        </template>
                                        <span v-if="media.title">{{ media.title }} - </span>
                                        {{ media.description }}
                                        <span v-if="media.creator">Created by: {{ media.creator }}. </span>
                                        <span v-if="media.owner">Provided by: {{ media.owner }}.</span>
                                    </div>
                                </q-card>
                            </q-intersection>
                        </div>
                    </q-expansion-item>
                </template>
            </div>
        </template>
    `,
    data() {
        return {
            clientRoot: Vue.ref(CLIENT_ROOT)
        }
    }
};
