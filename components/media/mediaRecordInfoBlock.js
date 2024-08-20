const mediaRecordInfoBlock = {
    props: {
        mediaData: {
            type: Object,
            default: null
        },
        editor: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card>
            <q-card-section>
                <div class="full-width row justify-between q-col-gutter-sm">
                    <div class="col-3 column">
                        <div v-if="mediaData.format.startsWith('audio') || mediaData.format.startsWith('video')" class="full-width row justify-center">
                            <template v-if="mediaData.format.startsWith('audio')">
                                <audio class="media-thumbnail" controls>
                                    <source :src="mediaData.accessuri" :type="mediaData.format">
                                </audio>
                            </template>
                            <template v-else>
                                <video class="media-thumbnail" controls>
                                    <source :src="mediaData.accessuri" :type="mediaData.format">
                                </video>
                            </template>
                        </div>
                        <div class="q-mt-xs full-width row justify-center q-gutter-sm text-bold">
                            <span v-if="mediaData.format.startsWith('video')">
                                <a :href="mediaData.accessuri" target="_blank">Full Size</a>
                            </span>
                            <span v-else-if="!mediaData.format.startsWith('audio')">
                                <a :href="mediaData.accessuri" target="_blank">Download File</a>
                            </span>
                        </div>
                    </div>
                    <div class="col-8 column">
                        <template v-if="editor">
                            <div v-if="mediaData.sortsequence">
                                <span class="text-bold">Sort Sequence: </span>{{ mediaData.sortsequence }}
                            </div>
                            <div v-if="mediaData.accessuri">
                                <span class="text-bold">URL: </span>{{ mediaData.accessuri }}
                            </div>
                            <div v-if="mediaData.sourceurl">
                                <span class="text-bold">Source URL: </span>{{ mediaData.sourceurl }}
                            </div>
                        </template>
                        <div v-if="mediaData.title">
                            <span class="text-bold">Title: </span>{{ mediaData.title }}
                        </div>
                        <div v-if="mediaData.description">
                            <span class="text-bold">Description: </span>{{ mediaData.description }}
                        </div>
                        <div v-if="mediaData.creator">
                            <span class="text-bold">Creator: </span>{{ mediaData.creator }}
                        </div>
                        <div v-if="mediaData.owner">
                            <span class="text-bold">Owner: </span>{{ mediaData.owner }}
                        </div>
                        <div v-if="mediaData.language">
                            <span class="text-bold">Language: </span>{{ mediaData.language }}
                        </div>
                        <div v-if="mediaData.usageterms">
                            <span class="text-bold">Usage Terms: </span>{{ mediaData.usageterms }}
                        </div>
                        <div v-if="mediaData.rights">
                            <span class="text-bold">Rights: </span>{{ mediaData.rights }}
                        </div>
                        <div v-if="mediaData.publisher">
                            <span class="text-bold">Publisher: </span>{{ mediaData.publisher }}
                        </div>
                        <div v-if="mediaData.contributor">
                            <span class="text-bold">Contributor: </span>{{ mediaData.contributor }}
                        </div>
                        <div v-if="mediaData.locationcreated">
                            <span class="text-bold">Location Created: </span>{{ mediaData.locationcreated }}
                        </div>
                        <div v-if="mediaData.bibliographiccitation">
                            <span class="text-bold">Bibliographic Citation: </span>{{ mediaData.bibliographiccitation }}
                        </div>
                        <div v-if="mediaData.furtherinformationurl">
                            <span class="text-bold">Further Information URL: </span>{{ mediaData.furtherinformationurl }}
                        </div>
                    </div>
                    <div v-if="editor" class="col-1 row justify-end">
                        <div>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openEditorPopup(mediaData['mediaid']);" icon="fas fa-edit" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit media record
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showMediaEditorPopup">
            <media-editor-popup
                :media-id="editMediaId"
                :show-popup="showMediaEditorPopup"
                @close:popup="showMediaEditorPopup = false"
            ></media-editor-popup>
        </template>
    `,
    components: {
        'media-editor-popup': mediaEditorPopup
    },
    setup() {
        const editMediaId = Vue.ref(0);
        const showMediaEditorPopup = Vue.ref(false);

        function openEditorPopup(id) {
            editMediaId.value = id;
            showMediaEditorPopup.value = true;
        }

        return {
            editMediaId,
            showMediaEditorPopup,
            openEditorPopup
        }
    }
};
